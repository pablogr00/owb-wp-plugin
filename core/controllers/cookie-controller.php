<?php

class CookieManager {
  // Crear una nueva cookie
  public static function setCookie($name, $value, $days) {
      $value = urlencode($value);
      $expires = time() + ($days * 24 * 60 * 60);
      setcookie($name, $value, $expires, "/", "", false, true); // Ruta "/" y `httponly` activado
  }

  // Recuperar el valor de una cookie
  public static function getCookie($name) {
      if (isset($_COOKIE[$name])) {
          $value = urldecode($_COOKIE[$name]);
          return $value;
      }
      return null;
  }
}