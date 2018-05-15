<?php

namespace App\Exceptions;

use Exception;

class JsonDecodeException extends Exception {

  /*
   * @param $response the code that could not be decoded
   * @see Exception
   */
  public function __construct($response, $code = 0, Exception $previous = null) {

      $message = "Could not decode API response to JSON: '".$response."'.";

      parent::__construct($message, $code, $previous);
  }

}
