<?php
/*
 * Poll controller class to handle all session related operations
 */ 
class PollController extends ControllerBase implements IController
{  
  // Get card set array of the session
  private function getIndex($session, $voteValue)
  {
    $cardSet = $this->getCardSet($session);
    return array_flip($cardSet)[$voteValue];
  }

  // Get value of session and index
  private function getValue($session, $vote)
  {
    $cardSet = $this->getCardSet($session);
    $value = $cardSet[$vote->getValue()];
    return intval($value);
  }

  // Start a new poll in the session
  private function startPoll($sessionId, $topic)
  {
    $session = $this->getSession($sessionId);
      
    // Start new poll
    $poll = new Poll();
    $poll->setTopic($topic);
    $poll->setSession($session);   
    $poll->setResult(-1);   
    
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
    include __DIR__ .  "/session-evaluation.php";

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
    $voteIndex = $this->getIndex($session, $voteValue);
    $match->setValue($voteIndex);
    
    // Evaluate the poll
    if(SessionEvaluation::evaluatePoll($session, $currentPoll))
    {
      $cardSet = $this->getCardSet($session);
      SessionEvaluation::highlightVotes($session, $currentPoll, $cardSet);
    }
        
    // Save all to db
    $this->saveAll([$match, $currentPoll]);
    $this->saveAll($currentPoll->getVotes()->toArray());
  }
  
  // Wrap up current poll in reponse object
  private function currentPoll($sessionId)
  {
    // Load the user-vote.php required for this
    include __DIR__ .  "/user-vote.php";
    
    $session = $this->getSession($sessionId);
    $cardSet = $this->getCardSet($session);
    
    // Create response array
    $votes = array();
    $currentPoll = $session->getCurrentPoll();
    foreach($session->getMembers() as $index=>$member)
    {
      $votes[$index] = UserVote::create($member, $currentPoll, $cardSet);
    }

    // Create reponse object
    $response = new stdClass();
    $response->name = $session->getName();
    $response->topic = $currentPoll != null ? $currentPoll->getTopic() : "";
    // Time taken for estimation
    if($currentPoll != null)
    {
      $diff = $currentPoll->getEndTime()->diff($currentPoll->getStartTime());
      $response->duration = new stdClass();
      $response->duration->min = $diff->i;
      $response->duration->sec = $diff->s;
    }
    // Vote estimation
    $response->votes = $votes;
    $response->flipped = is_null($currentPoll) ? false : $currentPoll->getResult() >= 0;
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
        $result->votable = is_null($currentPoll) ? false : $currentPoll->getResult() < 0;
        
        return $result;
    }
  }
}

return new PollController($entityManager, $cardSets);
?>