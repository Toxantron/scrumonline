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

$flipped = is_null($currentPoll) ? false : $currentPoll->getResult() > 0;

// Evaluate min and max if necessary
if($flipped)
{
  // Calculate frequency of each value
  $sum = 0;
  $cards = [1 => new CardFrequency(1), 2 => new CardFrequency(2), 3 => new CardFrequency(3), 5 => new CardFrequency(5), 
            8 => new CardFrequency(8), 13 => new CardFrequency(13), 20 => new CardFrequency(20), 40 => new CardFrequency(40),
            100 => new CardFrequency(100)];
  foreach($votes as $vote)
  {
    $cards[$vote->value]->count++;
    $sum += $vote->value;
  }
  
  // Determine most common vote
  foreach($cards as $card)
  {
    if(!isset($mostCommon) || $mostCommon->count < $card->count)
      $mostCommon = $card;
  }
  
  // Selector for highest or lowest bid
  $selector = function ($votes, $frequency, $mostCommon)
  {
    // No card at all
    if($frequency->count == 0)
      return 0;
    // This is the most common, no lowest found
    if($frequency == $mostCommon)
      return -1;
    
    foreach($votes as $vote)
    {
      if($vote->value == $frequency->value)
        $vote->active = true;
    }
    return 1;
  };
  $min = 0; $max = 0;
  // Iterate over frequencies and find lowest
  foreach($cards as $card)
  {
    $min = $selector($votes, $card, $mostCommon);
    if($min != 0) 
      break;
  }
  // Iterate over frequencies and find highest
  foreach(array_reverse($cards) as $card)
  {
    $max = $selector($votes, $card, $mostCommon);
    if($max != 0)
      break;
  }
}

$response = new stdClass();
$response->votes = $votes;
$response->flipped = $flipped;
$response->consensus = $min == -1 && $max == -1;

echo json_encode($response);
