<?php 
require_once "../config.php";
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
