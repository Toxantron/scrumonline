<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Proxy\AbstractProxyFactory;

require_once __DIR__ . "/../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Annotations
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/model"));
$config->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_NEVER);

// database configuration parameters
$conn = array(
    'dbname' => 'scrum_online',
    'user' => 'toxantron',
    'password' => 'scrumonline',
    'host' => 'fuchsserver.de',
    'driver' => 'pdo_mysql',
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

// Load models
require_once __DIR__ . "/model/member.php";
require_once __DIR__ . "/model/poll.php";
require_once __DIR__ . "/model/session.php";
require_once __DIR__ . "/model/vote.php";

// Create controller base class
class ControllerBase
{
  protected $entityManager;
  
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
  
  // Get member by id
  public function getMember($id)
  {
      $member = $this->entityManager->find("Member", $id);
      return $member;
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
}
