<?php
require_once "controller.php";

$sessions = $controller->getAllSessions();

$active = "Sessions";
include "../header.php";
?>
  
<div class="row">
  <div class="col-xs-12 col-md-8 col-md-offset-2">
    <div>
      <span class="left"><strong>Name</strong></span>
      <span class="center"><strong>Members</strong></span>
      <span class="right"><strong>Private</strong></span>
    </div>
    <div class="list-group">
    <?php foreach($sessions as $session): ?>
      <a class="list-group-item" href=<?php echo ("\"session.php?id=" . $session->getId() . "\"") ?>>
        <span class="left"><strong><?php echo $session->getName()?></strong></span>
        <span class="center"><strong><?php echo $session->getMembers()->count() ?></strong></span>
        <?php if($session->getIsPrivate() == true): ?>
          <span class="right"><span class="glyphicon glyphicon-lock"></span></span>
        <?php endif; ?>
      </a>
    <?php endforeach ?>
    </div>
  </div>
</div>

<?php include "../footer.html"; ?>