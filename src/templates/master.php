<?php
include __DIR__ . "/../config.php";

?>
<!-- Headline -->
<div class="row">
  <div class="col-xs-12">
    <h1>{{ master.id }} - {{ master.name }}</h1>
  </div>
</div>
      
<!-- Poll control -->
<div class="row topic">
  <div class="col-xs-12">
    <ul class="nav nav-tabs">
      <li ng-class="{active: master.current == source}" ng-repeat="source in master.sources| orderBy: 'position'">
        <a class="selectable" ng-click="master.selectSource(source)">{{ source.name }}</a>
      </li>
    </ul>
    <div class="ticketing" ng-include="master.current.view">
    </div>
  </div>
</div>
  
<!-- Live poll view -->
<div class="row">
    <div class="card-overview">

      <div ng-repeat="vote in master.votes track by vote.id" class="col-lg-2 col-sm-3 col-xs-4">        
        <div class="card-container">
          <div class="deletable-card">
            <div class="card-flip" ng-class="{flipped: master.flipped}">
              <div class="card front" ng-class="{active: vote.active}">
      	       <div ng-if="vote.placed" class="inner"><span class="card-label">?</span></div>
              </div>
              <div class="card back" ng-class="{active: vote.active, confirmed: master.consensus}">
    	          <div class="inner"><span class="card-label" ng-bind="vote.value"></span></div>
              </div>
            </div>
            <div class="delete-member selectable" ng-click="master.remove(vote.id)">
              <span class="glyphicon glyphicon-remove"></span>
            </div>
          </div>
          <h2 ng-bind="vote.name"></h2>
        </div>            
      </div>

    </div>
</div>
            
<div class="row">
  <div class="col-xs-12">
    <h2>Invite members</h1>
    <p>Invite members to join your session. Session id: <strong ng-bind="master.id"></strong></p>
    <p>Or send them this link: <a href="http://<?= $host ?>/#/join/{{ master.id }}"><?= $host ?>/#/join/{{ master.id }}</a>
  </div>
</div>
