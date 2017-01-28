<?php
/*
 * Session controller class to handle all session related operations
 */ 
class SessionController extends ControllerBase implements IController
{
  // Get all sessions from db
  private function getAllSessions()
  {
    // Create query finding all active sessions
    $query = $this->entityManager->createQuery('SELECT s.id, s.name, s.isPrivate, count(m.id) memberCount  FROM Session s LEFT JOIN s.members m WHERE s.lastAction > ?1 GROUP BY s.id');
    $query->setParameter(1, new DateTime('-2 hour'));
    $sessions = $query->getArrayResult();
    return $sessions;
  }
  
  // Create session with name and private flag
  private function createSession($name, $private, $password)
  {
    $session = new Session();
    $session->setName($name);
    $session->setIsPrivate($private);
    if ($private)
      $session->setPassword($this->createHash($password));
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
    
    // Store name in cookie if not done yet
    // if(!isset($_COOKIE['scrum_member_name']))
    //   setcookie('scrum_member_name', $name); European privacy law
    
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
  
  private function hasPassword($id)
  {
    $session = $this->getSession($id);
    return $session->getIsPrivate();
  }

  private function memberCheck($sid, $mid)
  {
    $session = $this->getSession($sid);
    foreach($session->getMembers() as $member) {
      if($member->getId() == $mid) {
        return true;
      }
    }
    return false;
  }
  
  private function checkPassword($id, $password)
  {
    $session = $this->getSession($id);
    return $session->getPassword() === $this->createHash($password);
  }
  
  private function createHash($password)
  {
    return hash('md5', $password);
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

      case "membercheck":
        return $this->memberCheck($_GET["sid"], $_GET["mid"]);
        
      case "protected":
        $id = $_GET["id"];
        return $this->hasPassword($id);
        
      case "check":
        $data = $this->jsonInput();
        return $this->checkPassword($data["id"], $data["password"]);
    }
  }
}

return new SessionController($entityManager);
?>
