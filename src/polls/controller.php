<?php
require_once __DIR__ . "/../bootstrap.php";

/*
 * Poll controller class to handle all session related operations
 */ 
class PollController
{
  private $entityManager;
  
  function __construct($entityManager)
  {
      $this->entityManager = $entityManager;
  }
}

$controller = new SessionController($entityManager);
?>