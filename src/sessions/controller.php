<?php
require_once __DIR__ . "/../bootstrap.php";

/*
 * Session controller class to handle all session related operations
 */ 
class SessionController
{
  private $entityManager;
  
  function __construct($entityManager)
  {
      $this->entityManager = $entityManager;
  }
  
  // Get session by id
  public function getSession($id)
  {
      $session = $this->entityManager->find("Session", $id);
      return $session;
  }
  
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

      $this->entityManager->persist($session);
      $this->entityManager->flush();
    
      return $session;
  }
  
  // Add a member with this name to the session
  public function addMember($id, $name)
  {
      $session = $entityManager->find("Session", $id);

      $member = new Member();
      $member->setName($name);
      $member->setSession($session);

      $entityManager->persist($member);
      $entityManager->flush();
    
      return [
        "session" => $session,
        "member" => $member
      ];
  }
}

$controller = new SessionController($entityManager);
?>
