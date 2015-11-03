<?php
include "model/session.php";

require_once "bootstrap.php";

$session = new Session();
$name = $_GET["name"];
$session->setName($name);

$entityManager->persist($session);
$entityManager->flush();
?>
  
<p>Database id: <?php echo $session->getId() ?><p>