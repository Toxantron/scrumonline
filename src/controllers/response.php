<?php
class Response
{  
  // Indicator if operation was successful
  public $success;
  
  // Result object
  public $result;
  
  // Error message on server side
  public $error;  
  
  // Create response object for successful operation
  public static function createSuccess($result)
  {
    $response = new Response();
    $response->success = true;
    $response->result = $result;
    return $response;
  }
  
  // Create reponse object for failed operation
  public static function createFailure($error)
  {
    $response = new Response();
    $response->success = false;
    $response->error = $error;
    return $response;
  }
}