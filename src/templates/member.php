<?php 
include __DIR__ . "/../config.php";

?>
<div data-ng-init="cards=[<?php foreach($cards as $card)
  echo '{value: '.$card.', active: false},';
?>]">
</div>

<div class="row">
  <div class="col-md-8 col-md-offset-2 col-xs-12">
    <h2 data-ng-bind="topic"></h2>
  </div>
</div>
      
<div class="row">
  <div class="col-lg-2 col-md-3 col-xs-4" data-ng-repeat="card in cards">
    <div class="card-container">
      <div class="card selectable" data-ng-class="{active: card.active, confirmed: card.confirmed}" data-ng-click="selectCard(card)">
        <div class="inner"><span class="card-label" data-ng-bind="card.value">1</span></div>
      </div>
    </div>
  </div>
</div>
        
<div class="row">
  <div class="col-md-8 col-md-offset-2 col-xs-12">
    <h2>How to:</h2>
    <p>
      Votes can only be placed during an active poll.That means as long as the master has not started a poll or all votes have been placed, you can not vote!
      When you select a card it is highlighted in red, meaning that you vote is processed by the server. If it was placed successfully the card is highlighted
      green as feedback. Until everone has placed their vote you can still change it. When the last person votes the poll is closed.
    </p>
  </div>
</div>
