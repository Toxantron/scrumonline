<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Proxy\AbstractProxyFactory;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/config.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/model"));
$config->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_NEVER);
$config->setProxyDir(__DIR__ . '/proxies');

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

// Load models
require_once __DIR__ . "/model/member.php";
require_once __DIR__ . "/model/poll.php";
require_once __DIR__ . "/model/session.php";
require_once __DIR__ . "/model/vote.php";

/*
 * Base class for all controllers
 */
class ControllerBase
{
  protected $entityManager;

  // Configured cards sets
  public $cardSets;
  
  function __construct($entityManager, $cardSets = [])
  {
    $this->entityManager = $entityManager;
    $this->cardSets = $cardSets;
  }
  
  // Get session by id
  protected function getSession($id)
  {
    $session = $this->entityManager->find("Session", $id);
    if($session == null)
      throw new Exception("Unknown session id!");
    return $session;
  }
  
  // Get member by id
  protected function getMember($id)
  {
    $member = $this->entityManager->find("Member", $id);
    return $member;
  }

  // Get card set of the session
  protected function getCardSet($session)
  {
    return $this->cardSets[$session->getCardSet()];
  }
  
  protected function jsonInput()
  {
    $post = file_get_contents('php://input');
    $data = json_decode($post, true);

	 return $data;
  }
  
  // Save only a single entity
  protected function save($entity)
  {
    $this->entityManager->persist($entity);
    $this->entityManager->flush();
  }
  
  // Save an array of entities
  protected function saveAll(array $entities)
  {
    foreach($entities as $entity)
    {
      $this->entityManager->persist($entity);
    }
    $this->entityManager->flush();
  }

  // Create a crypto hash for a secret using the name as salt
  protected function createHash($name, $password)
  {
    // Create a safe token from name, password and salt
    $token = crypt($name . $password, '$1$ScrumSalt');
    $fragments = explode('$', $token);
    $hash = $fragments[sizeof($fragments) - 1];
    return $hash;
  }

  // The cookie name of the token for a given session
  protected function tokenKey($id)
  {
    return 'session-token-' . $id;
  }

  // Make sure the caller has the necesarry token for the operation
  // $memberName indicates that a member token is sufficient for the operation
  // $privateOnly indicates that the token is only required for private sessions
  protected function verifyToken($session, $memberName = null, $privateOnly = false)
  {
    if ($this->tokenProvided($session, $memberName, $privateOnly))
      return true;

    http_response_code(403); // Return HTTP 403 FORBIDDEN
    return false;
  }

  // Check if the token for this session was present in the request
  // Return true if it was and otherwise false
  protected function tokenProvided($session, $memberName = null, $privateOnly = false)
  {
    // Token only required for private sessions and this is a public one
    if ($privateOnly && $session->getIsPrivate() == false)
      return true;

    // Everything else requires a token
    $tokenKey = $this->tokenKey($session->getId());
    if (!isset($_COOKIE[$tokenKey]))
      return false;
    
    // Verify token
    $token = $_COOKIE[$tokenKey];
    if ($token == $session->getToken())
      return true;

    // Alternatively compare membertoken if sufficient
    if ($memberName != null && $token == $this->createHash($memberName, $session->getToken()))
      return true;

    // Token required but not provided
    return false;
  }
}
