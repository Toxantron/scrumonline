<?php
require_once "controller.php";

$id = $_GET["id"];
$session = $controller->getSession($id);

include "../header.html";
include "../navigation.php";
?>

<div class="container-fluid main">
  <div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      <h1><?php echo $id . " - " . $session->getName(); ?></h1>
    </div>
  </div>
      
  <div data-ng-app="master-view" class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-12">
      
      <div class="card-overview" data-ng-init="votes = [
      <?php
        // Init model from db
        foreach($session->getMembers() as $member)
          echo ('{id: ' . $member->getId() . ', name: \'' . $member->getName() . '\', value: 0, placed: false, flipped: false},');
      ?>]">

        <div data-ng-repeat="vote in votes track by vote.id" class="col-lg-2 col-md-3 col-xs-4">        
          <div class="card-container">
            <div class="card-flip" data-ngClass="{flipped: flipped}">
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
  
<?php inlcude("scripts.php"); ?>
<script type="text/javascript">
  var app = angular.module('master-view', []);
  app.controller('pollCtrl', function($scope, $http){
    setInterval(function(){
      $http.get("/polls/current.php").success(function(response){
        $scope.votes[0].flipped = true;
      });
  	 }, 1000)
  });
</script>
<?php include("footer.html"); ?>