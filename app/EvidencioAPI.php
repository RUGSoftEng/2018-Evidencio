<?php

namespace App;

use Exceptions\JsonDecodeException;

class EvidencioAPI
{
  /**
   * Helper method to perform an API call
   * @param string $path URL to the call without leading "https://www.evidencio.com/api/"
   * @param array $params list of parameters as a key-value array
   * @return array API response converted into a PHP variable
   * @throws JsonDecodeException if JSON response could not be decoded
   * Other possible exceptions are listed in Guzzle's documentation
   */
  private static function fetch($path, $params = [])
  {
    $client = new \GuzzleHttp\Client(['base_uri' => 'https://www.evidencio.com/api/']);
    $res = $client->post($path,
    [
      'headers' => [
        'Accept' => 'application/json',
        'Authorization' => config('app.evidencio_key')
      ],
      'form_params' => $params
    ]);

    $json = json_decode($res->getBody(),true);

    if(is_null($json))
    {
      throw new JsonDecodeException($res->getBody());
    }
    return $json;
  }

  /**
   * Get basic Evidencio model statistics
   * @return array decoded JSON containing number of models that you are
   * authorised to use and number of your own models (created using that API key)
   */
  public static function overview()
  {
    return self::fetch("overview");
  }

  /**
   * Search fro Evidencio models
   * @param string $query phrase to search for
   * @return array decoded JSON containing all the Evidencio models (with all
   * their data) matching the phrase
   */
  public static function search($query)
  {
    return self::fetch("search",["query" => $query]);
  }

  /**
   * Get all Evidencio models
   * @return array decoded JSON containing all Evidencio models with all their data
   */
  public static function models()
  {
    return self::fetch("models");
  }

  /**
   * Get Evidencio model
   * @param int $id requested Evidencio model id
   * @return array decoded JSON containing requested Evidencio model data
   */
  public static function getModel($id)
  {
    return self::fetch("model",["id" => $id]);
  }

  /**
   * Run Evidencio model
   * @param int $id Evidencio model id
   * @param array $values a key-value array containing Evidencio model variable
   * ids and their corresponding values
   * @return array decoded JSON containing the result and additional information
   */
  public static function run($id,$values)
  {
    foreach ($values as $value) {
      if (is_numeric($value)) {
        $value = floatval($value);
      }
    }
    $values["id"] = $id;
    return self::fetch("run",$values);
  }

}
