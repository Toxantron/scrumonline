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

$response = new stdClass();
$response->votes = $votes;
$response->flipped = is_null($currentPoll) ? false : $currentPoll->getResult() > 0;

echo json_encode($response);
