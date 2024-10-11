<?php
require_once plugin_dir_path(__FILE__) . 'class-api-client.php';

class Odoo_API_Client extends API_Client {

  private $db;
  private $username;
  private $password;
  private $cookieFile;
  public $uid;

  public function __construct($url, $db, $username, $password) {
    parent::__construct($url);
    $this->db = $db;
    $this->username = $username;
    $this->password = $password;
    $this->cookieFile = plugin_dir_path(__FILE__) . 'cookies.txt';
  }

  public function authenticate() {
    $payload = [
      'jsonrpc' => '2.0',
      'params' => [
        'db' => $this->db,
        'login' => $this->username,
        'password' => $this->password
      ]
    ];

    // Hacer la solicitud POST utilizando el método post() del padre (API_Client)
    $response = $this->post('web/session/authenticate', $payload);

    // Verificar si la respuesta es un WP_Error
    if (is_wp_error($response)) {
      error_log('Error en la autenticación: ' . print_r($response, true));
      return $response;
    }
    // Verificar la respuesta de la API
    if (isset($response['result']['uid'])) {
      $this->uid = $response['result']['uid'];
      return $this->uid;
    } else {
      // Si no hay UID, loguear la respuesta completa para depuración
      return new WP_Error('auth_error', 'Autenticación fallida: no se pudo obtener el UID.', $response);
    }
  }

  // Método para hacer llamadas a otros métodos en la API de Odoo
  public function call($model, $method, $args = array(), $fields, $kwargs = array()) {
    $data = [
      'jsonrpc' => '2.0',
      'method'  => 'call',
      'params'  => [
        'service' => 'object',
        'method'  => 'execute_kw',
        'args'    => [
          $this->db,
          $this->uid,
          $this->password,
          $model,
          $method,
          $args
        ],
        'kwargs'  => $kwargs
      ],
      'id'  => uniqid()
    ];

    $response =  $this->post('jsonrpc', $data);

    // Verificar si la respuesta es un WP_Error
    if (is_wp_error($response)) {
      error_log('Error en la petición: ' . print_r($response, true));
      return $response;
    }

    return $response;
  }
}
