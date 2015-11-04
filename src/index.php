<?php 
	include("header.html");
	include("navigation.php");
?>
  <!-- Add your site or application content here -->
  <div class="container-fluid main" >
    <div class="row">
      <div class="col-lg-1 col-lg-offset-4 col-md-2 col-md-offset-3 col-xs-4 col-xs-offset-4">
			<form action="/sessions/create.php" method="post">
            <p>Name:<input type="text" name="name"></p>
            <p>Is private:<input type="checkbox" name="isPrivate"></p>
            <input type="submit" class="btn btn-lg btn-default" value="Create session">
			</form>
      </div>
      <div class="col-lg-1 col-lg-offset-2 col-md-2 col-md-offset-2 col-xs-4 col-xs-offset-4">
      	<form action="/sessions/session.php" method="get">
            <p>Id:<input type="text" name="id"></p>
				<input type="submit" class="btn btn-lg btn-default" value="Join session">
			</form>
      </div>
    </div>
  </div>

<?php include("footer.html"); ?>
