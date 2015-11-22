<?php
require_once "controller.php";

$session = $controller->getSession($_GET["sid"]);
$currentPoll = $session->getCurrentPoll();
$topic = 

// Result object. Only votable until all votes received
$result = new stdClass();
$result->topic = is_null($currentPoll) ? "No topic" : $currentPoll->getTopic();
$result->votable = is_null($currentPoll) ? false : $currentPoll->getResult() == 0;

echo json_encode($result);