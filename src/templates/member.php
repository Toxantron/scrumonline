<?php 
include __DIR__ . "/../config.php";

?>

<div class="panel panel-default">
  <div class="panel-heading">
    <div class="row">
      <a ng-href="{{member.topicUrl}}" target="_blank"><h2 class="col-xs-10" ng-bind="member.topic"></h2></a>
      <div class="col-xs-2">
        <div class="leave remove selectable" ng-click="member.leave()">
          <span class="glyphicon glyphicon-remove"></span>
        </div>
      </div>
    </div>
  </div>
  <div class="panel-body" style="white-space: pre-line">
    <div ng-bind-html="member.description"></div>
  </div>
</div>

<div class="row">
  <div class="col-lg-2 col-md-3 col-xs-4" ng-repeat="card in member.cards">
    <div class="card-container">
      <div class="card selectable" ng-class="{active: card.active, confirmed: card.confirmed}" ng-click="member.selectCard(card)">
        <div class="inner">
          <span class="card-label" ng-bind-html="card.value"></span>
	      </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <h2 class="col-xs-12">How to:</h2>
  <p class="col-xs-12">
    Votes can only be placed during an active poll.That means as long as the master has not started a poll or all votes have been placed, you can not vote!
    When you select a card it is highlighted in red, meaning that you vote is processed by the server. If it was placed successfully the card is highlighted
    green as feedback. Until everyone has placed their vote you can still change it. When the last person votes the poll is closed.
  </p>
</div>
