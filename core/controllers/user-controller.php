<?php
require_once plugin_dir_path(__FILE__) . '../api/class-odoo-api-client.php';

function warehouse_user_save_data(){
  $odoo_client = new Odoo_API_Client('https://pablo1.odoo.com', 'pablo1', 'pablogar07@gmail.com', '*yq5@_7.Mv&q2N2');
  $odoo_client->authenticate();

  // Obtener usuario  
  $current_user = wp_get_current_user();
  $username = $current_user->user_login;

  $args[] = [["name", "=", $username]];
  $kwargs = ["id", "name", "lot_stock_id"];

  $result = $odoo_client->call('stock.warehouse', 'search_read', $args, $kwargs);

  $warehouse_data = [];

  foreach($result['result'] as $item){
    $id = $item['id'];
    $name = $item['name'];
    $lot_stock_id = $item['lot_stock_id'][0];

    $warehouse_data = [
      'id'            => $id,
      'name'          => $name,
      'lot_stock_id'  => $lot_stock_id
    ];
  }
  
  // Almacenar los datos del almacén en los meta datos del usuario
  update_user_meta($current_user->ID, 'warehouse_data', $warehouse_data);
}