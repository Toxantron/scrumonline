<?php
/*
 * Session controller class to handle all session related operations
 */ 
class SessionController extends ControllerBase
{
  // Get all sessions from db
  // URL: /api/session/active
  public function active()
  {
    // Create query finding all active sessions
    $query = $this->entityManager->createQuery('SELECT s.id, s.name, s.isPrivate, s.token, count(m.id) memberCount  FROM Session s LEFT JOIN s.members m WHERE s.lastAction > ?1 GROUP BY s.id');
    $query->setParameter(1, new DateTime('-1 hour'));
    $sessions = $query->getArrayResult();

    // Determine password requirement for each session
    foreach($sessions as &$session) {
      $tokenKey = $this->tokenKey($session["id"]);
      $session["requiresPassword"] = $session["isPrivate"] 
        && (!isset($_COOKIE[$tokenKey]) || $_COOKIE[$tokenKey] !== $session["token"]);
      // Remove token from the response again
      unset($session["token"]);
    }

    return $sessions;
  }
  
  // Create session with name and private flag
  // URL: /api/session/create
  public function create()
  {
    $data = $this->jsonInput();        

    $session = new Session();
    $session->setName($data["name"]);
    $session->setCardSet($data["cardSet"]);

    // Generate the access token and assign it to the session
    $private = $data["isPrivate"];
    $session->setIsPrivate($private);
    if ($private)
      $token = $this->createHash($data["name"], $data["password"]);
    else
      $token = $this->createHash($data["name"], bin2hex(random_bytes(8)));   
    $session->setToken($token);
      
    $session->setLastAction(new DateTime());

    $this->save($session);
    
    $this->setCookie($session);
    
    return new NumericResponse($session->getId());
  }

  // Add or remove member
  // URL: /api/session/member/{id}/?{mid}
  public function member($sessionId, $memberId = 0)
  {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "PUT")
    {           
      return $this->addMember($sessionId);
    }
    if ($method == "DELETE")
    {
      $this->removeMember($memberId);
    }
  }
  
  // Add a member with this name to the session
  private function addMember($id)
  {   
    $data = $this->jsonInput();
    $name = $data["name"];
    
    $session = $this->getSession($id);
    $token = $session->getToken();
    $tokenKey = $this->tokenKey($session->getId());

    // Check for existing member
    foreach($session->getMembers() as $candidate)
    {
      if($candidate->getName() == $name)
      {
        $member = $candidate;
        break;
      }  
    }

    // This blocks check different ways to be granted access
    if ($this->tokenProvided($session, $name)) {
      // The user already has the token so we do nothing and continue
    } else if(isset($_GET["token"]) && $_GET["token"] == $token) {
      // User supplied the token
      $this->setCookie($session);
    } else if(isset($data["password"]) && $token === $this->createHash($session->getName(), $data["password"])) {
      // Or the password
      $this->setCookie($session);
    } else if ($session->getIsPrivate() == false && !isset($member)) {
      // Tokens are only required to join private sessions,
      // but without the token the rights are restricted by a member-only token
      // unless this member already exists
      $memberToken = $this->createHash($name, $token);
      $this->setCookie($session, $memberToken);
    } else {
      // Return access forbidden otherwise
      http_response_code(403); // Return HTTP 403 FORBIDDEN
      return;
    }

    // Create new member
    if(!isset($member))
    {
      $member = new Member();
      $member->setName($name);
      $member->setSession($session);
      $session->setLastAction(new DateTime());
        
      $this->saveAll([$member, $session]);
    }

    // Set name cookie for faster login next time
    setcookie('scrum_member_name', $name, time()+60*60*24*30, "/");
    
    // Create response
    $result = new stdClass();
    $result->sessionId = $id;
    $result->memberId = $member->getId();
    return $result;
  }
  
  // Remove member from session
  private function removeMember($id)
  {
    // Get member and session
    $member = $this->getMember($id); 
    $session = $member->getSession();

    if (!$this->verifyToken($session, $member->getName()))
      return;

    // Get and remove member       
    $this->entityManager->remove($member);
    $this->entityManager->flush();

    // Reevaluate the current poll
    include __DIR__ .  "/session-evaluation.php";
    $poll = $session->getCurrentPoll();
    if($poll !== null && SessionEvaluation::evaluatePoll($session, $poll))
    {
      $cardSet = $this->getCardSet($session);
      SessionEvaluation::highlightVotes($session, $poll, $cardSet);
    }

    // Update session to trigger polling
    $session->setLastAction(new DateTime());
    $this->save($session);

    $this->entityManager->flush();
  }
  
  // Check if session is protected by password
  // This only returns true of the requesting user does not
  // have the token cookie
  // URL: /api/session/requiresPassword/{id}
  public function requiresPassword($id = 0)
  {
    $session = $this->getSession($id);
    $requires = $session->getIsPrivate() && !$this->tokenProvided($session);
    return new BoolResponse($requires);
  }

  // Check if member is still part of the session
  // URL: /api/session/membercheck/{id}/{mid}
  public function membercheck($sid, $mid)
  {
    $session = $this->getSession($sid);
    foreach($session->getMembers() as $member) {
      if($member->getId() == $mid) {
        return new BoolResponse(true);
      }
    }
    return new BoolResponse();
  }
  
  // Check given password for a session
  // URL: /api/session/check/{id}
  public function check($id)
  {
    $data = $this->jsonInput();
    $session = $this->getSession($id);
    $result = $session->getToken() === $this->createHash($session->getName(), $data["password"]);

    // If the correct password was transmitted we grant the token as a reward
    if ($result)
      $this->setCookie($session);

    return new BoolResponse($result);
  }

  // Get the card set of this session
  // URL: /api/session/cardset/{id}
  public function cardset($id)
  {
    $session = $this->getSession($id);
    return $this->getCardSet($session);
  }

  // Get the card set of this session
  // URL: /api/session/cardsets
  public function cardsets()
  {
    return $this->cardSets;
  }

  // Set the token cookie for this session 
  // with additional parameters for expiration and path
  private function setCookie($session, $token = null)
  {
    $tokenKey = $this->tokenKey($session->getId());

    if ($token == null)
      $token = $session->getToken();

    setcookie($tokenKey, $token, time()+60*60*24*30, "/");
  }
}

return new SessionController($entityManager, $cardSets);
