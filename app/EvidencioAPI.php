<?php

namespace App;

class EvidencioAPI
{
  /*
   * @return API response converted into a PHP variable.
   * @throws JsonDecodeException if JSON response could not be decoded.
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
    $json = json_decode($res->getBody());
    if(is_null($json))
    {
      throw new Exceptions\JsonDecodeException("Could not decode API response to JSON: '".$res->getBody()."'.");
    }
    return $json;
  }

  public static function overview()
  {
    return self::fetch("overview");
  }

  public static function search($query)
  {
    return self::fetch("search",["query" => $query]);
  }

  public static function models()
  {
    return self::fetch("models");
  }

  public static function getModel($id)
  {
    return self::fetch("model",["id" => $id]);
  }

  /*
   * @param $values array of key-value pairs for each variable where id is the key
   */
  public static function run($id,$values)
  {
    $values["id"] = $id;
    return self::fetch("run",$values);
  }

}
