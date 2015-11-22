<?php
require_once __DIR__ . "/../bootstrap.php";

/*
 * Poll controller class to handle all session related operations
 */ 
class PollController extends ControllerBase
{  
  // Start a new poll in the session
  public function startPoll($sessionId, $topic)
  {
    $session = $this->getSession($sessionId);
    
    // Start new poll
    $poll = new Poll();
    $poll->setTopic($topic);
    $poll->setSession($session);
    
    $session->setCurrentPoll($poll);
    
    // Set or result result
    $poll->setResult(0);
    
    // Save changes
    $this->saveAll([$session, $poll]);
    
    return $poll;
  }
  
  // Place a vote for the current pull
  public function placeVote($sessionId, $memberId, $voteValue)
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
}

$controller = new PollController($entityManager);
?>