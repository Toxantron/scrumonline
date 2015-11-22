<?php

require_once "controller.php";
require_once "user-vote.php";

$sessionId = $_GET["id"];
$session = $controller->getSession($sessionId);

// Create response array
$votes = array();
$index = 0;
$currentPoll = $session->getCurrentPoll();
foreach($session->getMembers() as $member)
{
  $votes[$index++] = UserVote::create($member, $currentPoll);
}

echo json_encode($votes);
