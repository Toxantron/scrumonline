<?php
/*
 * Poll controller class to handle all session related operations
 */ 
class PollController extends ControllerBase
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

  // Check if the session as changed since the last polling call
  private function sessionUnchanged($session)
  {
    // Check if anything changed since the last polling call
    return isset($_GET['last']) && $_GET['last'] >= $session->getLastAction()->getTimestamp();
  }
  
  // Place a vote for the current poll
  // URL: /api/poll/vote/{id}/{mid}
  public function vote($sessionId, $memberId)
  {
    $voteValue = $data = $this->jsonInput()["vote"];

    include __DIR__ .  "/session-evaluation.php";

    // Fetch entities
    $session = $this->getSession($sessionId);
    $session->setLastAction(new DateTime());
    $currentPoll = $session->getCurrentPoll();
    $member = $this->getMember($memberId);
    
    // Reject votes if poll is completed
    if($currentPoll == null || $currentPoll->getResult() > 0)
      throw new Exception("Can not vote without poll or on completed polls!");
    
    // Find or create vote
    foreach($currentPoll->getVotes() as $vote)
    {
      if($vote->getMember() != $member)
        continue;

      $match = $vote;
      break;
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
    $this->saveAll([$session, $match, $currentPoll]);
    $this->saveAll($currentPoll->getVotes()->toArray());
  }
  
  // Wrap up current poll in reponse object
  // URL: /api/poll/current/{id}
  public function current($sessionId)
  {
    // Load the user-vote.php required for this
    include __DIR__ .  "/user-vote.php";

    // Create reponse object
    $response = new stdClass();
    $session = $this->getSession($sessionId);

    // Check if anything changed since the last polling call
    if($this->sessionUnchanged($session))
    {
      $response->unchanged = true;
      return $response;
    }
    
    // Fill response object
    $response->name = $session->getName();
    $response->timestamp = $session->getLastAction()->getTimestamp();
    $response->votes = array();
    // Include votes in response
    $currentPoll = $session->getCurrentPoll();
    if ($currentPoll == null)
    {
      $response->topic = "";
      $response->flipped = false;
      $response->consensus = false;
    }
    else
    {
      $response->topic = $currentPoll->getTopic();
      $response->flipped = $currentPoll->getResult() >= 0;
      $response->consensus = $currentPoll->getConsensus();

      $diff = $currentPoll->getEndTime()->diff($currentPoll->getStartTime());
      $response->duration = $diff;
    } 

    // Members votes
    $cardSet = $this->getCardSet($session);
    $query = $this->entityManager
      ->createQuery('SELECT m.id, m.name, v.value, v.highlighted FROM member m LEFT JOIN m.votes v WITH (v.member = m AND v.poll = ?1) WHERE m.session = ?2')
      ->setParameter(1, $currentPoll)
      ->setParameter(2, $session);
    $result = $query->getArrayResult();
    foreach($result as $vote)
      $response->votes[] = UserVote::fromQuery($cardSet, $vote);
    
    return $response;
  }

  // Get or set topic of the current poll
  public function topic($sessionId)
  {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "POST")
    {
      $data = $this->jsonInput();        
      $this->startPoll($sessionId, $data["topic"]);
      return null;
    }

    $result = new stdClass();
    $session = $this->getSession($sessionId);

    // Check if anything changed since the last polling call
    if($this->sessionUnchanged($session))
    {
      $result->unchanged = true;
      return $result;
    }

    $currentPoll = $session->getCurrentPoll();

    // Result object. Only votable until all votes received
    $result->timestamp = $session->getLastAction()->getTimestamp();
    if ($currentPoll == null)
    {
        $result->topic = "No topic";
        $result->votable = false;
    }
    else
    {
        $result->topic = $currentPoll->getTopic();
        $result->votable = $currentPoll->getResult() < 0;
    }

    return $result;
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
}

return new PollController($entityManager, $cardSets);
