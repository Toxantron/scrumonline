<?php 
include "controller.php";

$session = $controller->getSession($_GET["sid"]);
$currentPoll = $session->getCurrentPoll();
$topic = is_null($currentPoll) ? "No topic" : $currentPoll->getTopic();
$memberId = $_GET["mid"];

$cards = [1,2,3,5,8,13,20,40,100];

include "../header.html";
include "../navigation.php";
?>

<div class="container-fluid main" data-ng-app="scrum-online"><div data-ng-controller="cardController">
<div class="row">
  <div class="col-md-8 col-md-offset-2 col-xs-12">
    <h2 data-ng-bind="topic"></h1>
  </div>
</div>
<div class="row">
  <div class="col-lg-2 col-md-3 col-xs-4" data-ng-repeat="card in cards">
    <div class="card-container">
      <div class="card selectable" data-ng-class="{active: card == currentCard}" data-ng-click="selectCard(card.value)">
      	<div class="inner"><h1 data-ng-bind="card.value">1</h1></div>
      </div>
    </div>
  </div>
</div>
<div data-ng-if="votable" class="row">
  <div class="col-md-2 col-md-offset-5 col-xs-10 col-xs-offset-1">
    <button class="btn btn-lg btn-default" data-ng-click="placeVote()">Vote!</button>
  </div>
</div>
</div></div>

<?php include "../scripts.html"; ?>
<script type="text/javascript">
  // Controller for current poll
  scrum.controller('cardController', ['$scope', '$http', function($scope, $http) {
    // Int model
    $scope.id = <?= $session->getId() ?>;
    $scope.member = <?= $memberId ?>;
    $scope.topic = '<?= $topic ?>';
    
    $scope.cards = [<?php
    foreach($cards as $card) 
    {
    	echo '{value: '.$card.', active: false},';         
    }?>];
    
    $scope.selectCard = function(cardValue){ selectCard($scope, $http, cardValue); };
    
    $scope.placeVote = function() { placeVote($scope, $http); };
    
    $scope.votable = false;
    fetchTopic($scope, $http);
  }]);
</script>
<?php include "../footer.html"; ?>
