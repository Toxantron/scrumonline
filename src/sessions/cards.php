<?php 
include "controller.php";

$session = $controller->getSession($_GET["sid"]);
$currentPoll = $session->getCurrentPoll();
$topic = is_null($currentPoll) ? "'No topic'" : "'".$currentPoll->getTopic()."'";
$memberId = $_GET["mid"];

$cards = [1,2,3,5,8,13,20,40,100];

$active = "Sessions";
include "../header.php";
?>

<div data-ng-controller="cardController">
<div data-ng-init="id=<?= $session->getId() ?>; member=<?= $memberId ?>; topic=<?= $topic ?>; cards=[<?php
foreach($cards as $card)
  echo '{value: '.$card.', active: false},';
?>]"></div>
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      <h2 data-ng-bind="topic"></h1>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-2 col-md-3 col-xs-4" data-ng-repeat="card in cards">
      <div class="card-container">
        <div class="card selectable" data-ng-class="{active: card == currentCard}" data-ng-click="selectCard(card)">
          <div class="inner"><h1 data-ng-bind="card.value">1</h1></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include "../footer.html"; ?>
