<?php

require_once plugin_dir_path(__FILE__) . '../api/class-odoo-api-client.php';

class Warehouses_Controller {

  private $warehouse;

  public function __construct($warehouse)
  {
    $this->warehouse = $warehouse;
  }

  // Obtener stock por el nombre COMPLETO
  public function get_stock_by_warehouse() {
    $odoo_client = new Odoo_API_Client('https://pablo1.odoo.com', 'pablo1', 'pablogar07@gmail.com', '*yq5@_7.Mv&q2N2');
    $odoo_client->authenticate();

    $conditions = [[["warehouse_id", "ilike", $this->warehouse]]];
    $fields = ['product_id', 'quantity'];
    
    $result = $odoo_client->call('stock.quant', 'search_read', $conditions, $fields);

    // Manejo de errores: Verificar si la respuesta es válida
    if (is_wp_error($result)) {
      return new WP_Error('api_error', 'Error en la llamada a la API: ' . $result->get_error_message());
    }

    // Verificar si el resultado contiene datos
    if (empty($result['result'])) {
      return new WP_Error('no_data', 'No se encontraron datos para el almacén: ' . $this->warehouse);
    }

    // Filtramos el resultado para almacenarlo en un array mas sencillo
    $stock_data = [];
  
    foreach($result['result'] as $item){
      $product_id = $item['product_id']['0'];
      $product_name = $item['product_id']['1'];
      $quantity = $item['quantity'];

      $stock_data[] = [
        'product_id'    => $product_id,
        'product_name'  => $product_name,
        'quantity'      => $quantity
      ];
    }

    return $stock_data;
  }

  public function get_requested_stock() {
    $odoo_client = new Odoo_API_Client('https://pablo1.odoo.com', 'pablo1', 'pablogar07@gmail.com', '*yq5@_7.Mv&q2N2');
    $odoo_client->authenticate();

    $conditions = [[
      ["picking_id.state", "!=", "done"],
      ["picking_id.picking_type_id", "=", 7],
      ["picking_id.location_dest_id", "=", 30]
    ]];
    $fields = [
      'fields' => [
          "id", "name", "product_id", "product_uom_qty", "picking_id", "state", "location_dest_id"
      ]
    ];

     $result = $odoo_client->call('stock.move', 'search_read', $conditions, $fields);
 
    // Manejo de errores: Verificar si la respuesta es válida
    if (is_wp_error($result)) {
      return new WP_Error('api_error', 'Error en la llamada a la API: ' . $result->get_error_message());
    }

    // Verificar si el resultado contiene datos
    if (empty($result['result'])) {
      return new WP_Error('no_data', 'No se encontraron datos para el almacén: ' . $this->warehouse);
    }

    // Filtramos el resultado para almacenarlo en un array mas sencillo
    $picking_data = [];
  
    foreach($result['result'] as $item){
      $product_id = $item['product_id']['0'];
      $product_name = $item['product_id']['1'];
      $quantity = $item['product_qty'];
      $location_dest_id = $item['location_dest_id']['1'];
      $state = $item['state'];

      $picking_data[] = [
        'product_id'        => $product_id,
        'product_name'      => $product_name,
        'quantity'          => $quantity,
        'location_dest_id'  => $location_dest_id,
        'state'             => $state
      ];
    }

    return $picking_data;
  }

  public function get_warehouse_by_company($company) {
    $odoo_client = new Odoo_API_Client('https://pablo1.odoo.com', 'pablo1', 'pablogar07@gmail.com', '*yq5@_7.Mv&q2N2');
    $odoo_client->authenticate();

    $conditions = [[['name', 'ilike', $company]]];
    $fields = ['name', 'lot_stock_id'];

    $result = $odoo_client->call('stock.warehouse', 'search_read', $conditions, $fields);

    $warehouses = [];

    foreach ($result as $warehouse) {
      $warehouses[] = [
        'lot_stock_id' => $warehouse['lot_stock_id'][1],
        'name' => $warehouse['name']
      ];
    }

    return $warehouses;
  }
}