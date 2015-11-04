<?php
require_once "../bootstrap.php";

$name = $_POST["name"];
$private = $_POST["isPrivate"] == "on";

$session = new Session();
$session->setName($name);
$session->setIsPrivate($private);
$session->setLastAction(new DateTime());

$entityManager->persist($session);
$entityManager->flush();

header("Location: /sessions/session.php?id=" . $session->getId());
?>