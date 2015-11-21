<?php
require_once __DIR__ . "/../bootstrap.php";

/*
 * Session controller class to handle all session related operations
 */ 
class SessionController extends ControllerBase
{
  // Get all sessions from db
  public function getAllSessions()
  {
      $sessionRepo = $this->entityManager->getRepository("Session");
      $sessions = $sessionRepo->findAll();
      return $sessions;
  }
  
  // Create session with name and private flag
  public function createSession($name, $private)
  {
      $session = new Session();
      $session->setName($name);
      $session->setIsPrivate($private);
      $session->setLastAction(new DateTime());

      $this->save($session);
    
      return $session;
  }
  
  // Add a member with this name to the session
  public function addMember($id, $name)
  {
      $session = $entityManager->find("Session", $id);

      $member = new Member();
      $member->setName($name);
      $member->setSession($session);

      $this->save($member);
    
      return [
        "session" => $session,
        "member" => $member
      ];
  }
}

$controller = new SessionController($entityManager);
?>
