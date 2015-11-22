<?php
require_once "controller.php";

$id = $_POST["id"];
$name = $_POST["member-name"];

$resultArray = $controller->addMember($id, $name);

header("Location: /sessions/cards.php?sid=".$resultArray["session"]->getId()."&mid=".$resultArray["member"]->getId());
?>
