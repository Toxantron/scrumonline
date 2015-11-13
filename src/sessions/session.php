<?php
require_once "controller.php";

$id = $_GET["id"];
$session = $controller->getSession($id);

include "../header.html";
include "../navigation.php";
?>

<div class="container-fluid main">
  <div class="col-md-8 col-md-offset-2 col-xs-12">
    <h1><?php echo $id . " - " . $session->getName(); ?></h1>
  
    <div class="row">
      <div class="card-overview">
        <?php foreach($session->getMembers() as $member): ?>
        <div class="col-lg-2 col-md-3 col-xs-4">
        
          <div id="<?php echo $member->getId(); ?>" class="card-container">
            <div class="card-flip">
              <div class="card front">
      	       <div class="inner"><h1>?</h1></div>
              </div>
              <div class="card back">
      	       <div class="inner"><h1><?php echo $member->getId(); ?></h1></div>
              </div>
            </div>
            <h2><?php echo $member->getName(); ?></h2>
          </div>
            
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
        
<script type="text/javascript">
  setInterval(function(){
    $(".card-flip").toggleClass("flipped");
}, 5000)
</script>
  
<?php include("../footer.html"); ?>