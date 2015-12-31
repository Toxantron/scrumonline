<?php
require_once __DIR__ . "/../bootstrap.php";

/*
 * Session controller class to handle all session related operations
 */ 
class SessionController extends ControllerBase
{
  // Get all sessions from db
  private function getAllSessions()
  {
    $sessionRepo = $this->entityManager->getRepository("Session");
    $sessions = $sessionRepo->findAll();
    
    // Delete old sessions, filter active ones
    $index = 0;
    $activeSessions = array();
    foreach($sessions as $session)
    {
      // Delete old sessions
      if($session->getLastAction()->diff(new DateTime())->h > 2)
        $this->deleteSession($session);
      else
      {
        // Feth active ones
        $transformed = new stdClass();
        $transformed->id = $session->getId();
        $transformed->name = $session->getName();
        $transformed->memberCount = $session->getMembers()->count();
        $transformed->private = $session->getIsPrivate();
        $activeSessions[$index++] = $transformed;
      }
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
  private function createSession($name, $private)
  {
    $session = new Session();
    $session->setName($name);
    $session->setIsPrivate($private);
    $session->setLastAction(new DateTime());

    $this->save($session);
    
    return $session;
  }
  
  // Add a member with this name to the session
  private function addMember($id, $name)
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
  private function removeMember($id)
  {
    $member = $this->getMember($id);
    $this->entityManager->remove($member);
    $this->entityManager->flush();
  }
  
  public function execute()
  {
    switch($this->requestedMethod())
    {
      case "list": 
        return $this->getAllSessions();
        
      case "create":
        $post = file_get_contents('php://input');
        $data = json_decode($post, true);
        
        $session = $this->createSession($data["name"], $data["isPrivate"]);
        return $session->getId();
    }
  }
}

$controller = new SessionController($entityManager);

$result = $controller->execute();

echo json_encode($result);
?>
