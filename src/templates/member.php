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
  <article class="col-xs-12 col-lg-10 col-lg-offset-1">
  <h3>How to:</h3>
  <p>The panel at the top displays the current feature for estimation. The description below might help in deciding on an estimate for its complexity. 
    The panel is updated automatically everytime your scrum master starts a new poll. Once he did the server will accept estimates and you can start voting.
  </p>
  <p>Place your vote by selecting one of the cards above. The card will be highlighted red to indicate that the server is processing your vote. Once it did
    the card will be highlighted green and you should see a card with a <strong>?</strong> above your name on the master view. If it stays red that means
    the server rejected your vote. This is mostly the cause when you tried to vote outside of an open estimation poll. Either by voting before the scrum
    master started the session or after everyone voted.</p>
  <p>
    Until everyone voted you can change your vote. You can directly select a new card or, if you need more time to think, click your current card again to
    undo your selection and take back your vote. The poll will now remain open until you place a new estimate for the current feature.
  </p>
  <p>After all your team members voted on the current story, the poll is closed and the cards on the master view are now flipped. You can no longer vote on
    this session until the master restarts the estimation. If your vote on the master view is highlighted in red, it means you either gave the highest or
    lowest estimation. In that case explain the decision to you team members. After all arguments were heared your scrum master or product owner can start
    a new poll until you reach a consensus or verbally agree on a value withot voting again.</p>
</div>
