<?php
/*
Plugin Name: Odoo-WC Bridge Custom
Plugin URI: http://mi-sitio.com/mi-primer-plugin
Description: "WC-Odoo Sync Project" integra WooCommerce con Odoo.
Version: 1.0
Author: Pablo García Rodríguez
Author URI:
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'core/api/class-odoo-api-client.php';
require_once plugin_dir_path(__FILE__) . 'core/controllers/warehouse-stock-controller.php';
require_once plugin_dir_path(__FILE__) . 'core/controllers/user-controller.php';
require_once plugin_dir_path(__FILE__) . 'core/controllers/cookie-controller.php';

define( 'ODOO_WC_BRIDGE_VERSION', '1.0.0');
define( 'ODOO_WC_BRIDGE__FILE__', __FILE__ );
define( 'ODOO_WC_BRIDGE_PATH', plugin_dir_path( ODOO_WC_BRIDGE__FILE__ ) );
define( 'ODOO_WC_BRIDGE_URL', plugins_url('/', ODOO_WC_BRIDGE__FILE__ ) );
define( 'ODOO_WC_BRIDGE_ASSETS_PATH', ODOO_WC_BRIDGE_PATH . 'assets/' );

// Verifica si el usuario es colaborador y añade el panel de Residencia al 'header'
// Guarda los datos del almacén en los metadatos del usuario
function agregar_boton_para_colaboradores() {
  if (current_user_can('contributor')) {
      $link = get_page_link(610);
      echo '<button style="width: 40px; height: 40px; border-radius: 50%; border: none; background-color: red; display: flex; justify-content: center; align-items: center;">
              <a href="' . esc_url($link) . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="0.88em" height="1em" viewBox="0 0 448 512">
                  <path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0S96 57.3 96 128s57.3 128 128 128m89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4"/>
                </svg>
              </a>
            </button>';
      warehouse_user_save_data();
  }
}
add_shortcode('boton_colaborador', 'agregar_boton_para_colaboradores');

// Acceso a la tienda, comprueba si hay una ubicación ya establecida
add_action('template_redirect', function() {
  if (is_page('packs')) {
    $cookie_manager = new CookieManager;
    $cookie = $cookie_manager::getCookie('assigned_warehouse');

    if (!$cookie) {
      echo 'Cookie no establecida';
      
      // Pop-up
      include plugin_dir_path(__FILE__) . 'includes/views/location-popup.html';

      wp_enqueue_style('custom-popup-style', plugin_dir_url(__FILE__) . 'assets/css/location-popup.css');
      wp_enqueue_script('custom-popup-script', plugin_dir_url(__FILE__) . 'assets/js/location-popup.js', array(), null, true);
    }
  }
});

// Mostrar ubicación en el header
function mostrar_boton_ubicacion() {
  $cookie_manager = new CookieManager;
  $cookie = $cookie_manager::getCookie('assigned_warehouse');

  include plugin_dir_path(__FILE__) . 'core/models/warehouse-json-model.php';
  $JSONPath =  plugin_dir_path(__FILE__) . 'uploads/warehouses.json';

  $warehouse_manager = new WarehouseManager($JSONPath);
  $warehouse_name = $warehouse_manager->getWarehouseByLotStockId($cookie);

  if ($cookie) {
      ob_start();
      ?>
      <style>
        .warehouse-button {
          color: #fff;
          border: none;
          cursor: pointer;
        }
        .warehouse-button:hover {
          background-color: #005f8d;
        }
        #warehouse-btn-container{
          display: flex;
          align-items: center;
          flex-direction: column;
        }
        #custom-popup-container {
          display: none;
        }
      </style>

      <button class="warehouse-button">
        <div id="warehouse-btn-container">
          <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24">
          <path fill="currentColor" d="M19.717 20.362C21.143 19.585 22 18.587 22 17.5c0-1.152-.963-2.204-2.546-3C17.623 13.58 14.962 13 12 13s-5.623.58-7.454 1.5C2.963 15.296 2 16.348 2 17.5s.963 2.204 2.546 3C6.377 21.42 9.038 22 12 22c3.107 0 5.882-.637 7.717-1.638" opacity="0.5"/><path fill="currentColor" fill-rule="evenodd" d="M5 8.515C5 4.917 8.134 2 12 2s7 2.917 7 6.515c0 3.57-2.234 7.735-5.72 9.225a3.28 3.28 0 0 1-2.56 0C7.234 16.25 5 12.084 5 8.515M12 11a2 2 0 1 0 0-4a2 2 0 0 0 0 4" clip-rule="evenodd"/>
          </svg>
          <?php echo esc_html($warehouse_name); ?>
        </div>
      </button>

      <div id="custom-popup-container">
        <?php include plugin_dir_path(__FILE__) . 'includes/views/location-popup.html'; ?>
      </div>
      
      <?php
      return ob_get_clean();
  }
}
add_shortcode('boton_ubicacion', 'mostrar_boton_ubicacion');

// Encolar estilos en el frontend
function owb_enqueue_styles() {
  wp_enqueue_style(
      'location-popup',
      plugin_dir_url( __FILE__ ) . 'assets/css/location-popup.css',
      array(),
      '1.0.0'
  );

  wp_enqueue_style(
    'stock-panel',
    plugin_dir_url( __FILE__ ) . 'assets/css/stock-panel.css',
    array(),
    '1.0.0'
  );
}
add_action( 'wp_enqueue_scripts', 'owb_enqueue_styles' );

// Encolar scripts
function owb_enqueue_scripts() {
  wp_enqueue_script('owb-ajax-script', plugin_dir_url(__FILE__) . 'assets/js/ajax.js', array('jquery'), null, true);

  wp_localize_script('owb-ajax-script', 'ajax_object_ajax', array(
    'ajax_url'  => admin_url('admin-ajax.php'),
    'nonce'     => wp_create_nonce('owb_ajax_nonce'),
  ));

  wp_enqueue_script('owb-location-form-script', plugin_dir_url(__FILE__) . 'assets/js/location-popup.js', array('jquery'), null, true);

  wp_localize_script('owb-location-form-script', 'ajax_object_location', array(
    'ajax_url'  => admin_url('admin-ajax.php'),
    'nonce'     => wp_create_nonce('owb_ajax_nonce'),
  ));
}
add_action('wp_enqueue_scripts', 'owb_enqueue_scripts');

// Manejo de las solicitudes AJAX
function obw_handle_ajax_request() {
  // Verifica el nonce de seguridad
  check_ajax_referer('owb_ajax_nonce', 'nonce');

  // Asegura que la acción fue enviada
  if (!isset($_POST['custom_action'])) {
      wp_send_json_error('Acción no especificada.');
      return;
  }

  // Sanitiza la acción
  $custom_action = sanitize_text_field($_POST['custom_action']);

  // Convierte la cadena serializada del formulario en un array
  if (isset($_POST['form_data'])) {
      parse_str($_POST['form_data'], $form_data);
  }

  // Ruta base de los controladores
  $controllers_path = plugin_dir_path(__FILE__) . 'core/controllers/';

  $action_to_controller = array(
    'request_stock'     => 'request-stock.php',
    'warehouse_request' => 'request-warehouses.php',
  );

  if(array_key_exists($custom_action, $action_to_controller)) {
    $controller_file = $controllers_path . $action_to_controller[$custom_action];

    if(file_exists($controller_file)) {
      include $controller_file;
    } else {
      wp_send_json_error('El controlador no existe.');
      wp_die();
    } 
  } else {
    wp_send_json_error('Acción no reconocida.');
    wp_die();
  }
}
add_action('wp_ajax_obw_handle_ajax_request', 'obw_handle_ajax_request');
add_action('wp_ajax_nopriv_obw_handle_ajax_request', 'obw_handle_ajax_request');

// Manejo de las solicitudes AJAX para seleccionar el almacén
function obw_set_warehouse() {
  // Verifica el nonce de seguridad
  check_ajax_referer('owb_ajax_nonce', 'nonce');

  // Asegura que se ha enviado el warehouse_id
  if (!isset($_POST['warehouse_id'])) {
    wp_send_json_error('ID de almacén no proporcionado.');
    wp_die();
  }

  // Sanitiza el ID del almacén
  $warehouse_id = sanitize_text_field($_POST['warehouse_id']);

  $cookie_manager = new CookieManager;
  $cookie_manager->setCookie('assigned_warehouse', $warehouse_id, 30);

  $odoo_client = new Odoo_API_Client('https://pablo1.odoo.com', 'pablo1', 'pablogar07@gmail.com', '*yq5@_7.Mv&q2N2');

  // Autenticar con Odoo
  $auth = $odoo_client->authenticate();
  if (!$auth) {
    wp_send_json_error('Falló la autenticación con Odoo.');
    wp_die();
  }

  $args = [
    [
      ["location_id", "=", 36], 
      ["quantity", ">=", 0]
    ],
    ["location_id", "product_id", "quantity", "reserved_quantity", "warehouse_id"],
    0,
    50
  ];

  $result = $odoo_client->call('stock.quant', 'search_read', $args, []);

  // Enviar el contenido capturado y el mensaje de éxito en la respuesta JSON
  wp_send_json_success('Almacén seleccionado correctamente.');

  wp_die();
}
add_action('wp_ajax_obw_set_warehouse', 'obw_set_warehouse');
add_action('wp_ajax_nopriv_obw_set_warehouse', 'obw_set_warehouse');

// Mostrar el contenido del plugin (eliminar después de la depuración)
function mostrar_plugin($content) {

  if(is_page('panel-de-residencias') && is_singular()) {
    // Incluimos el archivo PHP que genera la tabla de productos
    ob_start(); // Iniciar el buffer de salida
    include 'includes/views/stock-panel.php'; // Cambia esta ruta a la ubicación correcta de tu archivo
    $tabla_productos = ob_get_clean(); // Guardar el contenido generado en una variable
    $content .= $tabla_productos; // Añadir la tabla al contenido

  }
  return $content;
}

add_filter('the_content', 'mostrar_plugin');