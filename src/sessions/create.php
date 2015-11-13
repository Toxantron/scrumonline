<?php
require_once "controller.php";

$name = $_POST["name"];
$private = $_POST["isPrivate"] == "on";

$session = $controller->createSession($name, $private);

header("Location: /sessions/session.php?id=" . $session->getId());
?>