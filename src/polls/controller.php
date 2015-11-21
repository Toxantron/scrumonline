<?php
require_once __DIR__ . "/../bootstrap.php";

/*
 * Poll controller class to handle all session related operations
 */ 
class PollController extends ControllerBase
{
  // Get current poll of the session
  public function getCurrentPoll($sessionId)
  {
      // Fetch the session
      $session = $this->getSession($sessionId);
      return $session->getCurrentPoll();      
  }
  
  // Start a new poll in the session
  public function startPoll($sessionId, $topic)
  {
      $session = $this->getSession($sessionId);
      
      $poll = new Poll();
      $poll->setTopic($topic);
      $poll->setSession($session);
    
      $session->setCurrentPoll($poll);
    
      $this->saveAll([$session, $poll]);
    
      return $poll;
  }
}

$controller = new SessionController($entityManager);
?>