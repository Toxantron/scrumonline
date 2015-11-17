<?php 
	include("../header.html");
	include("../navigation.php");

	$cards = [1,2,3,5,8,13,20,40,100];
?>

<div class="container-fluid main">
<div id="selection" class="row">
<?php foreach($cards as $card): ?>
  <div class="col-lg-2 col-md-3 col-xs-4">
    <div class="card-container">
      <div class="card selectable">
      	<div class="inner"><h1><?= $card ?></h1></div>
      </div>
    </div>
  </div>

<?php endforeach; ?>
</div>
</div>

<?php 
inlcude("scripts.php");
include("footer.html"); 
?>
