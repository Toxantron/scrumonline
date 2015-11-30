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
    
      // Delete old sessions, filter active ones
      $index = 0;
      $activeSessions = array();
      foreach($sessions as $session)
      {
         if($session->getLastAction()->diff(new DateTime())->h > 2)
           $this->deleteSession($session);
         else
           $activeSessions[$index++] = $session;
      }
    
      return $activeSessions;
  }
  
  private function deleteSession($session)
  {
      $session->setCurrentPoll(null);
      $this->save($session);
    
      $this->entityManager->remove($session);
      $this->entityManager->flush();
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
      $session = $this->getSession($id);
    
      // Check for existing member
      foreach($session->getMembers() as $candidate)
      {
        if($candidate->getName() == $name)
        {
          $member = $candidate;
          break;
        }  
      }

      // Create new member
      if(!isset($member))
      {
        $member = new Member();
        $member->setName($name);
        $member->setSession($session);
        
        $this->save($member);
      }
    
      return [
        "session" => $session,
        "member" => $member
      ];
  }
  
  // Remove member from session
  public function removeMember($id)
  {
    $member = $this->getMember($id);
    $this->entityManager->remove($member);
    $this->entityManager->flush();
  }
}

$controller = new SessionController($entityManager);
?>
