<?php
// Bootstrap the application
require_once "bootstrap.php";
require_once "controllers/response.php";

// Parse controller and method
$controllerName = $_GET["c"];
$method = $_GET['m'];

// Load controller and invoke method
$controller = include "controllers/" . $controllerName . "-controller.php";
// Pass both query arguments
if (isset($_GET["id"]) && isset($_GET["mid"]))
  $result = $controller->$method($_GET["id"], $_GET["mid"]);
// Pass only id
else if (isset($_GET["id"]))
  $result = $controller->$method($_GET["id"]);
// Pass no arguments
else
  $result = $controller->$method();

// Return reponse as JSON if this method has a return value
if (isset($result) && $result != null)
  echo json_encode($result);
