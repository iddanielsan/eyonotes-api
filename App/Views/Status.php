<?php

namespace App\Views;

class Status{
  public static function render_error($http_code, $class, $message){
    http_response_code($http_code);
    exit('{ "status": "error", "class": "'.$class.'", "message": "'.$message.'"}');
  }
}