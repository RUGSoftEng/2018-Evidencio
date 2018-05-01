<?php

namespace App;

class EvidencioAPI
{
  /*
   * Helper method to perform an API call
   * @param $path URL to the call without leading "https://www.evidencio.com/api/"
   * @param $params list of parameters as a key-value array
   * @return API response converted into a PHP variable
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
      throw new Exceptions\JsonDecodeException($res->getBody());
    }
    return $json;
  }

  /*
   * Get basic Evidencio model statistics
   * @return decoded JSON containing number of models that you are authorised to use and number of your own models (created using that API key)
   */
  public static function overview()
  {
    return self::fetch("overview");
  }

  /*
   * Search fro Evidencio models
   * @param $query phrase to search for
   * @return decoded JSON containing all the Evidencio models (with all their data) matching the phrase
   */
  public static function search($query)
  {
    return self::fetch("search",["query" => $query]);
  }

  /*
   * Get all Evidencio models
   * @return decoded JSON containing all Evidencio models with all their data
   */
  public static function models()
  {
    return self::fetch("models");
  }

  /*
   * Get Evidencio model
   * @param $id requested Evidencio model id
   * @return decoded JSON containing requested Evidencio model data
   */
  public static function getModel($id)
  {
    return self::fetch("model",["id" => $id]);
  }

  /*
   * Run Evidencio model
   * @param $id Evidencio model id
   * @param $values a key-value array containing Evidencio model variable ids and their corresponding values
   * @return decoded JSON containing the result and additional information
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
