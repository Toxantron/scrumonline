<?php
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
        $transformed->isPrivate = $session->getIsPrivate();
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
  private function createSession($name, $private, $password)
  {
    $session = new Session();
    $session->setName($name);
    $session->setIsPrivate($private);
    if ($private)
      $session->setPassword($password);
    $session->setLastAction(new DateTime());

    $this->save($session);
    
    return $session;
  }
  
  // Add a member with this name to the session
  private function addMember($id, $name)
  {
    // Check arguments
    if($id == null)
      throw new Exception("Id must not be empty!");
    if($name == null)
      throw new Exception("Name must not be empty!");
    
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
    
    $result = new stdClass();
    $result->sessionId = $id;
    $result->memberId = $member->getId();
    return $result;
  }
  
  // Remove member from session
  private function removeMember($id)
  {
    $member = $this->getMember($id);
    $this->entityManager->remove($member);
    $this->entityManager->flush();
  }
  
  private function checkPassword($id, $password)
  {
    $session = $this->getSession($id);
    return $session->getPassword() === $password;
  }
  
  public function execute()
  {
    switch($this->requestedMethod())
    {
      case "list": 
        return $this->getAllSessions();
        
      case "create":
        $data = $this->jsonInput();        
        $session = $this->createSession($data["name"], $data["isPrivate"], $data["password"]);
        return $session->getId();
        
      case "join":
        $data = $this->jsonInput();        
        return $this->addMember($data["id"], $data["name"]);
        
      case "remove":
        $data = $this->jsonInput();
        $this->removeMember($data["memberId"]);
        return null;
        
      case "check":
        $data = $this->jsonInput();
        return $this->checkPassword($data["id"], $data["password"]);
    }
  }
}

$controller = new SessionController($entityManager);
?>
