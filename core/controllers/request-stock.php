<?php
if (!defined('ABSPATH')) {
    exit;
}

// Sanitiza y valida los datos
if (!isset($form_data['product_id']) || !isset($form_data['quantity'])) {
    wp_send_json_error('Faltan parámetros.');
    wp_die();
}

$product_id = intval($form_data['product_id']);
$quantity = floatval($form_data['quantity']);

$user_id = get_current_user_id();
if (!$user_id) {
    wp_send_json_error('Usuario no autenticado.');
    wp_die();
}

$warehouse_data = get_user_meta($user_id, 'warehouse_data', true);

require_once plugin_dir_path(__FILE__) . '../api/class-odoo-api-client.php';

$odoo_client = new Odoo_API_Client('https://pablo1.odoo.com', 'pablo1', 'pablogar07@gmail.com', '*yq5@_7.Mv&q2N2');

// Autenticar con Odoo
$auth = $odoo_client->authenticate();
if (!$auth) {
    wp_send_json_error('Falló la autenticación con Odoo.');
    wp_die();
}

if (!isset($warehouse_data['lot_stock_id']) || empty($warehouse_data['lot_stock_id'])) {
    wp_send_json_error('El ID de la ubicación de destino (lot_stock_id) no está definido o es inválido.');
    wp_die();
}

$lot_stock_id = intval($warehouse_data['lot_stock_id']);

// Preparar los datos para enviar a Odoo
$picking_data = [
  'picking_type_id' => 7,
  'location_id' => 8,
  'location_dest_id' => $lot_stock_id,
  'move_type' => 'direct',
  'state' => 'draft',
  'move_ids_without_package' => [
      [
          0, 0, [
              'name' => 'Solicitud de stock',
              'product_id' => $product_id,
              'product_uom_qty' => $quantity,
              'product_uom' => 1,
              'location_id' => 8,
              'location_dest_id' => $lot_stock_id,
          ],
      ],
  ],
];

try {
  $result = $odoo_client->call('stock.picking', 'create', [$picking_data], []);
  wp_send_json_success('Solicitud enviada. Respuesta de Odoo: ' . $result['result']);
} catch (Exception $e) {
  wp_send_json_error('Error al enviar la solicitud a Odoo: ' . $e->getMessage());
}
wp_die();
