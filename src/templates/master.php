<?php
include __DIR__ . "/../config.php";

?>
<!-- Headline -->
<div class="row">
  <div class="col-md-10 col-md-offset-1 col-xs-12">
    <h1>{{ id }} - {{ name }}</h1>
  </div>
</div>
      
<!-- Poll control -->
<div class="row topic">
  <div class="col-md-10 col-md-offset-1 col-xs-12">
    <form role="form" class="form-inline">
      <div class="form-group">
        <label for="topic">Topic:</label>
        <input type="text" class="form-control topic" data-ng-model="topic" placeholder="#4711 Create foo">
        <button class="btn btn-default" data-ng-click="startPoll()">Start</button>
      </div>
    </form>
  </div>
</div>

<div data-ng-if="consensus" class="row">
  <div class="col-md-10 col-md-offset-1 col-xs-12">
    <div class="alert alert-success" role="alert">
      <strong>Estimation done!</strong> The team agreed on <strong data-ng-bind="votes[0].value"></strong>!
    </div>
  </div>
</div>
  
<!-- Live poll view -->
<div class="row">
  <div class="col-md-10 col-md-offset-1 col-xs-12">
      
    <div class="card-overview">

      <div data-ng-repeat="vote in votes track by vote.id" class="col-lg-2 col-md-3 col-xs-4">        
        <div class="card-container">
          <div class="deletable-card">
            <div class="card-flip" data-ng-class="{flipped: flipped}">
              <div class="card front" data-ng-class="{active: vote.active}">
      	       <div data-ng-if="vote.placed" class="inner"><span class="card-label">?</span></div>
              </div>
              <div class="card back" data-ng-class="{active: vote.active}">
    	          <div class="inner"><span class="card-label" data-ng-bind="vote.value"></span></div>
              </div>
            </div>
            <div class="delete-member selectable" data-ng-click="remove(vote.id)">
              <span class="glyphicon glyphicon-remove"></span>
            </div>
          </div>
          <h2 data-ng-bind="vote.name"></h2>
        </div>            
      </div>

    </div>
        
  </div>
</div>
            
<div class="row">
  <div class="col-md-10 col-md-offset-1 col-xs-12">
    <h2>Invite members</h1>
    <p>Invite members to join your session. Session id: <strong data-ng-bind="id"></strong></p>
    <p>Or send them this link: <a href="http://<?= $host ?>/#/join/{{ id }}"><?= $host ?>/#/join/{{ id }}</a>
  </div>
</div>