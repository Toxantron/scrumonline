<?php
require_once "../bootstrap.php";

$id = $_GET["id"];
$session = $entityManager->find("Session", $id);

include "../header.html";
include "../navigation.php";
?>

<div class="container-fluid main">
  <h1>Session: <?php echo $id; ?> - <?php echo $session->getName(); ?></h1>
</div>