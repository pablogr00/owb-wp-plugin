jQuery(document).ready(function($) {
  $('.ajax-form').submit(function(e) {
    e.preventDefault();

    var form = $(this)
    var customAction = form.data('action')
    var method = form.data('method') || 'POST'
    var formData = form.serialize();

    $.ajax({
      type: method,
      url: ajax_object_ajax.ajax_url,
      data: {
        action: 'obw_handle_ajax_request',
        nonce: ajax_object_ajax.nonce,
        custom_action: customAction,
        form_data: formData
      },
      success: function(res) {
        if(res.success) {
          console.log('Operacion exitosa: ' + res.data)
          form.find('input[name="quantity"]').val('');
        } else {
          console.error('Error: ' + res.data)
        }
      },
      error: function(err) {
        console.error('Ocurrió un error en la solicitud AJAX.')
      }
    })
  })
})