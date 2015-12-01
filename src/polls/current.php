<?php

require_once "controller.php";

$sessionId = $_GET["id"];
$response = $controller->currentPoll($sessionId);

echo json_encode($response);
