<?php
require_once "controller.php";

$sessions = $controller->getAllSessions();
?>
  
<div class="row">
  <div class="col-xs-12 col-md-8 col-md-offset-2">
    <span class="session-list left"><strong>Name</strong></span>
    <span class="session-list center"><strong>Members</strong></span>
    <span class="session-list right"><strong>Private</strong></span>
    <div class="list-group">
    <?php foreach($sessions as $session): ?>
      <a class="list-group-item" <?php echo $session->getIsPrivate() ? "" : ("href=\"session.php?id=" . $session->getId() . "\""); ?>>
        <span class="session-list left"><strong><?= $session->getName() ?></strong></span>
        <span class="session-list center"><strong><?= $session->getMembers()->count() ?></strong></span>
        <?php if($session->getIsPrivate() == true): ?>
          <span class="session-list right"><span class="glyphicon glyphicon-lock"></span></span>
        <?php endif; ?>
      </a>
    <?php endforeach ?>
    </div>
  </div>
</div>