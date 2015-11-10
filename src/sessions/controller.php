<?php
require_once __DIR__ . "../bootstrap.php";

/*
 * Session controller class to handle all session related operations
 */ 
public class SessionController
{
  public function getSession($id)
  {
      $session = $entityManager->find("Session", $id);
      return $session;
  }
  
  public function getAllSessions()
  {
      $sessionRepo = $entityManager->getRepository("Session");
      $sessions = $sessionRepo->findAll();
      return $sessions;
  }
  
  public function createSession($name, $isPrivate)
  {
      $session = new Session();
      $session->setName($name);
      $session->setIsPrivate($private);
      $session->setLastAction(new DateTime());

      $entityManager->persist($session);
      $entityManager->flush();
    
      return $session;
  }
}

$controller = new SessionController();
?>
