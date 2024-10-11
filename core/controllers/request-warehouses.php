<?php
if (!defined('ABSPATH')) {
  exit;
}

// Establece el encabezado Content-Type a application/json
header('Content-Type: application/json');

require_once plugin_dir_path(__FILE__) . '../api/class-odoo-api-client.php';

$company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';

if (empty($company)) {
  wp_send_json_error('No se proporcionó el parámetro "company".');
  wp_die();
}

$odoo_client = new Odoo_API_Client('https://pablo1.odoo.com', 'pablo1', 'pablogar07@gmail.com', '*yq5@_7.Mv&q2N2');
$odoo_client->authenticate();

$conditions = [[['name', 'ilike', $company]]];
$fields = ['name', 'lot_stock_id'];

$result = $odoo_client->call('stock.warehouse', 'search_read', $conditions, $fields);

$warehouses = [];

foreach ($result['result'] as $warehouse) {

  $warehouses[] = [
    'lot_stock_id' => $warehouse['lot_stock_id'][0],
    'name' => $warehouse['name']
  ];
}

wp_send_json_success($warehouses);
wp_die();