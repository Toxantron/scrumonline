<?php
// Bootstrap the application
require_once "bootstrap.php";
require_once "controllers/response.php";

// Load controller
$controllerName = $_GET["c"];
include "controllers/" . $controllerName . "-controller.php";

// Execute call on controller
try
{
  $result = $controller->execute();
  $response = Response::createSuccess($result);
}
catch (Exception $e)
{
  $response = Response::createFailure($e->getMessage());
}

// Return response as JSON
echo json_encode($response);