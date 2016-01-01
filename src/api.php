<?php
// Bootstrap the application
require_once "bootstrap.php";

// Load controller
$controllerName = $_GET["c"];
include "controllers/" . $controllerName . "-controller.php";

// Execute call on controller
$result = $controller->execute();
echo json_encode($result);