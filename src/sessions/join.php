<?php
require_once "controller.php";

// Fetch id from post or get
$id = $_POST["id"];
if(is_null($id))
  $id = $_GET["id"];
// Name allways comes via post
$name = $_POST["member-name"];

// Information complete, forward to card view
if(!is_null($id) && !is_null($name) && $name != "")
{
  $resultArray = $controller->addMember($id, $name);
  header("Location: /sessions/cards.php?sid=".$resultArray["session"]->getId()."&mid=".$resultArray["member"]->getId());
  exit();
}

include "../header.php";
?>
<div class"row">
  <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading">Join session</div>
      <div class="panel-body">  
        <form role="form" action="/sessions/join.php" method="post">
          <div class="form-group">
            <label for="id">Session id:</label>
            <input type="text" class="form-control"  name="id" placeholder="4711" value="<?= $id ?>">
          </div>
          <div class="form-group">
            <label for="member-name">Your name:</label>
            <input type="text" class="form-control"  name="member-name" placeholder="John">
          </div>
          <input type="submit" class="btn btn-default" value="Join">
       </form>
      </div>
    </div>        
  </div>
</div>
          
<?php include "../footer.html" ?>
