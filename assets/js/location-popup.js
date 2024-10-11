(function($) {
  $(document).ready(function() {
    const cookieName = "assigned_warehouse";
    const cookieValue = getCookie(cookieName);
    const $popup = $("#customPopup");
    const warehouseBtn = $(".warehouse-button")
    const $popupContainer = $("#custom-popup-container")

    if (!cookieValue) {
      console.log("No cookie found. Displaying popup.");
      $popup.css("display", "block");
    } else {
      console.log("Cookie found. Popup will not be displayed.");
    }

    warehouseBtn.on('click', function() {
      $popupContainer.css("display", "block");
      $popup.css("display", "block");
    })

    $("#closePopup").on('click', function() {
      $popup.css("display", "none");
    });

    const $step_1 = $('#step-1');
    const $step_2 = $('#step-2');
    const $step_3 = $('#step-3');

    const $envio_a_casa_btn = $('#envio-a-casa-btn');
    const $residencia_btn = $('#residencia-btn');

    $envio_a_casa_btn.on('click', function() {
      $popup.css("display", "none");
      // ------------------------------------------------------------
      // Añadir la creación de la cookie con id de envío a domicilio
      // document.cookie = "assigned_warehouse=home_delivery_id; path=/;";
      // Asegúrate de definir 'home_delivery_id' con el valor correcto
      // ------------------------------------------------------------
    });

    $residencia_btn.on('click', function() {
      $step_1.css('display', 'none');
      $step_2.css('display', 'flex');
    });

    $(document).on('click', '.company-option', function(e) {
      e.preventDefault();

      const selectedCompany = $(this).data('value');
      const customAction = 'warehouse_request';

      $.ajax({
        type: 'POST',
        url: ajax_object_location.ajax_url,
        dataType: 'json',
        data: {
          action: 'obw_handle_ajax_request',
          nonce: ajax_object_location.nonce,
          custom_action: customAction,
          company: selectedCompany
        },
        success: function(res) {
          if (res.success) {
            var warehouses = res.data;
            var htmlContent = '<h4>Almacenes disponibles:</h4><ul id="residencias">';

            // Recorrer los almacenes y generar el HTML
            $.each(warehouses, function(index, warehouse) {
              htmlContent += `<li><a href="#" class="warehouse-option option" data-value="${warehouse.lot_stock_id}">${warehouse.name}</a></li>`;
            });

            htmlContent += '</ul>';

            // Insertar el HTML generado en el elemento deseado
            $step_3.html(htmlContent);
            $step_2.css('display', 'none');
            $step_3.css('display', 'flex');
          } else {
            console.error('Error en la respuesta:', res.data);
          }
        },
        error: function(error) {
          console.error('Error en la solicitud AJAX:', error);
        }
      });
    });

    $(document).on('click', '.warehouse-option', function(e) {
      e.preventDefault();

      if(getCookie(cookieName)){
        deleteCookie(cookieName)
      }

      const selectedWarehouse = $(this).data('value');
      console.log("Selected Warehouse:", selectedWarehouse);

      // Enviar el ID del almacén seleccionado al servidor mediante AJAX
      $.ajax({
        type: 'POST',
        url: ajax_object_location.ajax_url,
        dataType: 'json',
        data: {
          action: 'obw_set_warehouse',
          nonce: ajax_object_location.nonce,
          warehouse_id: selectedWarehouse
        },
        success: function(res) {
          if (res.success) {
            console.log('Almacén seleccionado correctamente:', res.data);
            // La cookie se establecerá en el servidor
            $popup.css("display", "none");
          } else {
            console.error('Error al seleccionar el almacén:', res.data);
          }
        },
        error: function(error) {
          console.error('Error en la solicitud AJAX:', error);
        }
      });
    });

    // Función para obtener el valor de una cookie por su nombre
    function getCookie(cname) {
      const name = cname + "=";
      const decodedCookie = decodeURIComponent(document.cookie);
      const ca = decodedCookie.split(';');
      for(let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(name) == 0) {
          return c.substring(name.length, c.length);
        }
      }
      return "";
    }

    function deleteCookie(cname) {
      if (getCookie(cname) !== "") {
        document.cookie = cname + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        console.log(`Cookie ${cname} ha sido eliminada.`);
      } else {
        console.log(`Cookie ${cname} no existe.`);
      }
    }
  });
})(jQuery);
