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
  
  // Place or delete a vote for the current poll
  // URL: /api/poll/vote/{id}/{mid}
  public function vote($sessionId, $memberId)
  {
    // Fetch entities
    $session = $this->getSession($sessionId);
    $member = $this->getMember($memberId);
    $currentPoll = $session->getCurrentPoll();

    // Validate token before performing action
    if (!$this->verifyToken($session, $member->getName()))
      return;

    // Reject votes if poll is completed
    if($currentPoll == null || $currentPoll->getResult() > 0)
      throw new Exception("Can not modify non-existing or completed poll!");

    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "POST")
      $this->placeVote($session, $currentPoll, $member);
    else if ($method == "DELETE")
      $this->deleteVote($session, $currentPoll, $member);
  }

  // Place a new vote
  private function placeVote($session, $currentPoll, $member)
  {
    include __DIR__ .  "/session-evaluation.php";

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
    $voteValue = $data = $this->jsonInput()["vote"];
    $voteIndex = $this->getIndex($session, $voteValue);
    $match->setValue($voteIndex);
    
    // Evaluate the poll
    if(SessionEvaluation::evaluatePoll($session, $currentPoll))
    {
      $cardSet = $this->getCardSet($session);
      SessionEvaluation::highlightVotes($session, $currentPoll, $cardSet);
    }
        
    // Save all to db
    $session->setLastAction(new DateTime());
    $this->saveAll([$session, $match, $currentPoll]);
    $this->saveAll($currentPoll->getVotes()->toArray());
  }

  // Delete an already placed vote
  private function deleteVote($session, $currentPoll, $member)
  {
    // Find the vote of this member
    foreach($currentPoll->getVotes() as $vote)
    {
      if ($vote->getMember() == $member)
      {
        $match = $vote;
        break;
      }
    }

    if (!isset($match))
      return;

    // Remove vote and update timestamp
    $this->entityManager->remove($match);
    $session->setLastAction(new DateTime());
    $this->save($session);
    $this->entityManager->flush();
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

    // Validate token only to access the topic of private sessions
    if (!$this->verifyToken($session, null, true))
      return;
    
    // Fill response object
    $response->name = $session->getName();
    $response->timestamp = $session->getLastAction()->getTimestamp();
    $response->votes = array();
    // Include votes in response
    $currentPoll = $session->getCurrentPoll();
    if ($currentPoll == null)
    {
      $response->topic = "";
      $response->description = "";
      $response->url = "";
      $response->flipped = false;
      $response->consensus = false;
    }
    else
    {
      $response->topic = $currentPoll->getTopic();
      $response->description = $currentPoll->getDescription();
      $response->url = $currentPoll->getUrl();
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
    {
      $userVote = UserVote::fromQuery($cardSet, $vote);
      $userVote->canDelete = $this->tokenProvided($session, $userVote->name);
      $response->votes[] = $userVote;
    }
    
    return $response;
  }

  // Get or set topic of the current poll
  public function topic($sessionId)
  {
    $session = $this->getSession($sessionId);

    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "POST")
    {
      $data = $this->jsonInput();
      $this->startPoll($session, $data["topic"], $data["description"], $data["url"]);
      return null;
    }

    $result = new stdClass();
    // Check if anything changed since the last polling call
    if($this->sessionUnchanged($session))
    {
      $result->unchanged = true;
      return $result;
    }

    // Reading a sessions topic is only protected for private sessions
    if (!$this->verifyToken($session, null, true))
      return;

    $currentPoll = $session->getCurrentPoll();

    // Result object. Only votable until all votes received
    $result->timestamp = $session->getLastAction()->getTimestamp();
    if ($currentPoll == null)
    {
        $result->topic = "No topic";
        $result->description = "";
        $result->url = "";
        $result->votable = false;
    }
    else
    {
        $result->topic = $currentPoll->getTopic();
        $result->description = $currentPoll->getDescription();
        $result->url = $currentPoll->getUrl();
        $result->votable = $currentPoll->getResult() < 0;
    }

    return $result;
  }

  // Start a new poll in the session
  private function startPoll($session, $topic, $description, $url)
  {
    // Only the sessions main token holder can start a poll
    if (!$this->verifyToken($session))
      return;
      
    // Start new poll
    $poll = new Poll();
    $poll->setTopic($topic);
    $poll->setDescription($description);
    $poll->setUrl($url);
    $poll->setSession($session);   
    $poll->setResult(-1);   
    
    // Update session
    $session->setLastAction(new DateTime());
    $session->setCurrentPoll($poll);
    
    // Save changes
    $this->saveAll([$session, $poll]);
  }
}

return new PollController($entityManager, $cardSets);
