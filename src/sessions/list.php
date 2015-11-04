<?php
require_once "../bootstrap.php";

$sessionRepo = $entityManager->getRepository("Session");
$sessions = $sessionRepo->findAll();

$active = "Sessions";

include "../header.html";
include "../navigation.php";
?>
  
<div class="container-fluid main">
  <div class="row">
    <div class="list-group">
<?php foreach($sessions as $session): ?>
      <a class="list-group-item" href=<?php echo ("\"session.php?id=" . $session->getId() . "\"") ?>><?php echo $session->getName()?></a>
<?php endforeach ?>
    </div>
  </div>
</div>