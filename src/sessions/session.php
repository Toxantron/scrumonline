<?php
require_once "controller.php";

$id = $_GET["id"];
$session = $controller->getSession($id);

include "../header.html";
include "../navigation.php";
?>

<div data-ng-app="master-view" class="container-fluid main">
  <!-- Headline -->
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      <h1><?php echo $id . " - " . $session->getName(); ?></h1>
    </div>
  </div>
      
  <!-- Poll control -->
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      <div data-ng-controller="pollController">
        <form role="form" class="form-inline">
          <div class="form-group">
            <label for="topic">Topic:</label>
            <input type="text" class="form-control" data-ng-model="topic" placeholder="#4711 Create foo">
            <button class="btn btn-default" data-ng-click="startPoll()">Start</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Live poll view -->
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      
      <div class="card-overview" data-ng-controller="pollController">

        <div data-ng-repeat="vote in votes track by vote.id" class="col-lg-2 col-md-3 col-xs-4">        
          <div class="card-container">
            <div class="card-flip" data-ng-class="{flipped: vote.flipped}">
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
        
</div>
  
<?php include "../scripts.html"; ?>
<script type="text/javascript">
  var app = angular.module('master-view', []);

  // Controller for current poll
  app.controller('pollController', ['$scope', '$http', function($scope, $http) {
    // Int model from db
    $scope.id = <?php echo $session->getId(); ?>; 
     
    $scope.startPoll = function() { startPoll($scope, $http); };
    
    $scope.votes = [];
    
    pollVotes($scope, $http, 10);
  }]);
</script>
<?php include "../footer.html"; ?>