<?php
require_once "../bootstrap.php";

$id = $_POST["id"];
$name = $_POST["member-name"];

$session = $entityManager->find("Session", $id);

$member = new Member();
$member->setName($name);
$member->setSession($session);

$entityManager->persist($member);
$entityManager->flush();

session_start();
$_SESSION["member"] = $member->getId();

header("Location: /sessions/cards.php?id=" . $session->getId());
?>