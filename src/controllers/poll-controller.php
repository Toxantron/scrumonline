<?php
require_once __DIR__ . "/../bootstrap.php";

/*
 * Poll controller class to handle all session related operations
 */ 
class PollController extends ControllerBase
{  
  // Start a new poll in the session
  private function startPoll($sessionId, $topic)
  {
    $session = $this->getSession($sessionId);
    
    // Start new poll
    $poll = new Poll();
    $poll->setTopic($topic);
    $poll->setSession($session);
    
    // Update session
    $session->setLastAction(new DateTime());
    $session->setCurrentPoll($poll);
    
    // Set or result result
    $poll->setResult(0);
    
    // Save changes
    $this->saveAll([$session, $poll]);
    
    return $poll;
  }
  
  // Place a vote for the current pull
  private function placeVote($sessionId, $memberId, $voteValue)
  {
    // Fetch entities
    $session = $this->getSession($sessionId);
    $currentPoll = $session->getCurrentPoll();
    $member = $this->getMember($memberId);
    
    // Find or create vote
    foreach($currentPoll->getVotes() as $vote)
    {
      if($vote->getMember() == $member)
        $match = $vote;
    }
    
    // Create vote if not found
    if(!isset($match))
    {
      $match = new Vote();
      $match->setPoll($currentPoll);
      $match->setMember($member);
    }    
    // Set value
    $match->setValue($voteValue);
    
    // Evaluate current poll
    $this->evaluatePoll($session, $currentPoll);
        
    // Save all to db
    $this->saveAll([$match, $currentPoll]);
  }
  
  private function evaluatePoll($session, $currentPoll)
  {
    $sum = 0;
    $count = $currentPoll->getVotes()->count();
    if($count == $session->getMembers()->count())
    {
      foreach($currentPoll->getVotes() as $vote)
      {
        $sum += $vote->getValue();  
      }
      $currentPoll->setResult($sum / $count);
    } 
  }
  
  // Select highest or lowest estimates -  depends on direction of loop
  private function selectLimits($votes, $frequency, $mostCommon)
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
  }
  
  // Evaluate the estimates once each vote was placed
  private function evaluateEstimates($votes)
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
  
    $min = 0; $max = 0;
    // Iterate over frequencies and find lowest
    foreach($cards as $card)
    {
      $min = $this->selectLimits($votes, $card, $mostCommon);
      if($min != 0) 
        break;
    }
    // Iterate over frequencies and find highest
    foreach(array_reverse($cards) as $card)
    {
      $max = $this->selectLimits($votes, $card, $mostCommon);
      if($max != 0)
        break;
    }
    
    return $min == -1 && $max == -1;
  }
  
  // Wrap up current poll in reponse object
  private function currentPoll($sessionId)
  {
    // Load the user-vote.php required for this
    include "user-vote.php";
    
    $session = $this->getSession($sessionId);
    
    // Create response array
    $votes = array();
    $currentPoll = $session->getCurrentPoll();
    foreach($session->getMembers() as $index=>$member)
    {
      $votes[$index] = UserVote::create($member, $currentPoll);
    }

    // Is poll complete?
    $flipped = is_null($currentPoll) ? false : $currentPoll->getResult() > 0;

    // Evaluate min and max if poll is complete
    if($flipped)
      $consensus = $this->evaluateEstimates($votes);
    else
      $consensus = false;


    // Create reponse object
    $response = new stdClass();
    $response->votes = $votes;
    $response->flipped = $flipped;
    $response->consensus = $consensus;
    
    return $response;
  }
  
  public function execute()
  {
    switch($this->requestedMethod())
    {
      case "current":
        $sessionId = $_GET["id"];
        return $this->currentPoll($sessionId);
        
      case "start":
        $data = $this->jsonInput();
        
        $this->startPoll($data["sessionId"], $data["topic"]);
        return null;
        
      case "place":
        $data = $this->jsonInput();

        $this->placeVote($data["sessionId"], $data["memberId"], $data["vote"]);
        return null;
        
      case "topic":
        $session = $this->getSession($_GET["sid"]);
        $currentPoll = $session->getCurrentPoll();

        // Result object. Only votable until all votes received
        $result = new stdClass();
        $result->topic = is_null($currentPoll) ? "No topic" : $currentPoll->getTopic();
        $result->votable = is_null($currentPoll) ? false : $currentPoll->getResult() == 0;
        
        return $result;
    }
  }
}

$controller = new PollController($entityManager);

include "execute.php";
?>