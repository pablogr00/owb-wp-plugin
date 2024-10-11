<?php

class API_Client {

  private $base_url;
  private $headers;

  public function __construct($base_url, $token = null) {
    $this->base_url = rtrim($base_url, '/');
    $this->headers = array(
      'Authorization' => 'Bearer',
      'Content-Type'  => 'application/json'
    );
    if ($token) {
      $this->headers['Authorization'] = 'Bearer ' . $token;
    }
  }

  // Peticiones GET
  public function get($endpoint, $params = array()) {
    $url = $this->base_url . '/' . ltrim($endpoint, '/') . '?' . http_build_query($params);
    return $this->request('GET', $url);
  }

  // Peticiones POST
  public function post($endpoint, $data = array()) {
    $url = $this->base_url . '/' . ltrim($endpoint, '/');
    return $this->request('POST', $url, json_encode($data));
  }

  // Método genérico para manejar todas las peticiones
  private function request($method, $url, $body = null) {
    $args = array(
      'method'  => $method,
      'headers' => $this->headers,
    );
    if ($body) {
      $args['body'] = $body;
    }

    $response = wp_remote_request($url, $args);
    if (is_wp_error($response)) {
      return new WP_Error('api_error', 'Error en la petición API: ' . $response->get_error_message());
    }

    return json_decode(wp_remote_retrieve_body($response), true);
  }
}