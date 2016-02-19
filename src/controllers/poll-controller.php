<?php
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
    
    // Save changes
    $this->saveAll([$session, $poll]);
    
    return $poll;
  }
  
  // Place a vote for the current poll
  private function placeVote($sessionId, $memberId, $voteValue)
  {
    // Fetch entities
    $session = $this->getSession($sessionId);
    $currentPoll = $session->getCurrentPoll();
    $member = $this->getMember($memberId);
    
    // Reject votes if poll is completed
    if($currentPoll != null && $currentPoll->getResult() > 0)
      throw new Exception("Can not vote on completed polls!");
    
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
    
    // Evaluate the poll
    $this->evaluatePoll($session, $currentPoll);
    if($currentPoll->getResult() > 0)
      $this->highlightVotes($currentPoll);
        
    // Save all to db
    $this->saveAll([$match, $currentPoll]);
    $this->saveAll($currentPoll->getVotes()->toArray());
  }
  
  // Evaluate the polls average
  private function evaluatePoll($session, $currentPoll)
  {
    $sum = 0;
    $count = $currentPoll->getVotes()->count();
    
    if($count != $session->getMembers()->count())
      return;
    
    foreach($currentPoll->getVotes() as $vote)
    {
      $sum += $vote->getValue();  
    }
    $currentPoll->setResult($sum / $count); 
    $currentPoll->setEndTime(new DateTime());
  }
  
  // Highlight highest and lowest estimate
  private function highlightVotes($currentPoll)
  {
    include "user-vote.php";
    
    $votes = $currentPoll->getVotes();
    $cards = [1 => new CardFrequency(1), 2 => new CardFrequency(2), 3 => new CardFrequency(3), 5 => new CardFrequency(5), 
              8 => new CardFrequency(8), 13 => new CardFrequency(13), 20 => new CardFrequency(20), 40 => new CardFrequency(40),
              100 => new CardFrequency(100)];
    foreach($votes as $vote)
    {
      $cards[$vote->getValue()]->count++;
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
    
    $currentPoll->setConsensus($min == -1 && $max == -1);
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
      if($vote->getValue() == $frequency->value)
        $vote->setHighlighted(true);
    }
    return 1;
  }
  
  // Wrap up current poll in reponse object
  private function currentPoll($sessionId)
  {
    // Load the user-vote.php required for this
    include __DIR__ .  "/user-vote.php";
    
    $session = $this->getSession($sessionId);
    
    // Create response array
    $votes = array();
    $currentPoll = $session->getCurrentPoll();
    foreach($session->getMembers() as $index=>$member)
    {
      $votes[$index] = UserVote::create($member, $currentPoll);
    }

    // Create reponse object
    $response = new stdClass();
    $response->name = $session->getName();
    $response->topic = $currentPoll != null ? $currentPoll->getTopic() : "";
    // Time taken for estimation
    $diff = $currentPoll->getEndTime()->diff($currentPoll->getStartTime());
    $response->duration = new stdClass();
    $response->duration->min = $diff->i;
    $response->duration->sec = $diff->s;
    // Vote estimation
    $response->votes = $votes;
    $response->flipped = is_null($currentPoll) ? false : $currentPoll->getResult() > 0;
    $response->consensus = is_null($currentPoll) ? false : $currentPoll->getConsensus();
    
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
?>