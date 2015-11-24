<?php
require_once "controller.php";

$id = $_GET["id"];
$session = $controller->getSession($id);
$currentPoll = $session->getCurrentPoll();
$topic = is_null($currentPoll) ? "null" : "'".$currentPoll->getTopic()."'";

$active = "Sessions";
include "../header.php";
?>

<div data-ng-controller="pollController">
  <div data-ng-init="id=<?= $session->getId() ?>; topic=<?= $topic ?>"></div>
  <!-- Headline -->
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      <h1><?php echo $id . " - " . $session->getName(); ?></h1>
    </div>
  </div>
      
  <!-- Poll control -->
  <div class="row topic">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      <form role="form" class="form-inline">
        <div class="form-group">
          <label for="topic">Topic:</label>
          <input type="text" class="form-control" data-ng-model="topic" placeholder="#4711 Create foo">
          <button class="btn btn-default" data-ng-click="startPoll()">Start</button>
        </div>
      </form>
    </div>
  </div>
  
  <!-- Live poll view -->
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      
      <div class="card-overview">

        <div data-ng-repeat="vote in votes track by vote.id" class="col-lg-2 col-md-3 col-xs-4">        
          <div class="card-container">
            <div class="card-flip" data-ng-class="{flipped: flipped}">
              <div class="card front">
      	       <div data-ng-if="vote.placed" class="inner"><h1>?</h1></div>
              </div>
              <div class="card back">
      	       <div class="inner"><h1 data-ng-bind="vote.value"></h1></div>
              </div>
            </div>
            <h2 data-ng-bind="vote.name"></h2>
          </div>            
        </div>

      </div>
        
    </div>
  </div>
            
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      <h2>Invite members</h1>
      <p>Invite members to join your session. Session id: <strong><?= $id ?></strong></p>
      <p>Or send them this link: <a href="http://www.fuchsserver.de/sessions/join.php?id=<?= $id ?>">www.fuchsserver.de/sessions/join.php?id=<?= $id ?></a>
    </div>
  </div>
        
</div>
<?php include "../footer.html"; ?>