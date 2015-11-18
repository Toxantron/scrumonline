<?php
require_once "controller.php";

$id = $_POST["id"];

session_start();
// Check for existing session
if(!isset($_SESSION["id"]) || $_SESSION["id"] != $id)
{
   $name = $_POST["member-name"];

   $resultArray = $controller->addMember($id, $name);
   
   $_SESSION["id"] = $resultArray["session"]->getId();
   $_SESSION["member"] = $resultArray["member"]->getId();
}

header("Location: /sessions/cards.php?");
?>
