<?php 
	include "header.html";
	include "navigation.php";
?>
  <!-- Add your site or application content here -->
  <div class="container-fluid main" >
    
    <!-- Introduction -->
    <div class="row">
      <div class="col-md-8 col-md-offset-2 col-xs-12">
        <article>
          <p>
            <h2>Scrum Online</h2>
            Welcome to my little planning poker web app. Use of this app is free of charge for everyone. I do accept donations but they
            were not the main purpose of this page. As a scrum master just start a named session and invite your team to join you. It is
            recommended to display the scrum master view on the big screen (TV or projector) and let everyone else join via smartphone.
          </p>
        </article>
      </div>
    </div>
            
    <div class="row">
      <div class="col-md-8 col-md-offset-2 col-xs-12">
        <article>
          <p>
            <h2>Create or join a session</h2>
          </p>
        </article>
      </div>
      
      <!-- Create session panel -->
      <div class="col-md-4 col-md-offset-2 col-sm-5 col-sm-offset-1 col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading">Create session</div>
          <div class="panel-body">  
           <form role="form" action="/sessions/create.php" method="post">
              <div class="form-group">
                 <label for="name">Session name:</label>
                 <input type="text" class="form-control" name="name" placeholder="My session">
              </div>
              <div class="form-group">
                <label><input type="checkbox" name="isPrivate"> is private</label> 
              </div>
              <input type="submit" class="btn btn-default" value="Create">
           </form>
          </div>
      	</div>        
      </div>
            
      <!-- Join session panel -->
      <div class="col-md-4 col-sm-5 col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading">Join session</div>
          <div class="panel-body">  
           <form role="form" action="/sessions/join.php" method="post">
              <div class="form-group">
                 <label for="id">Session id:</label>
                 <input type="text" class="form-control"  name="id" placeholder="4711">
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
  </div>

<?php 
include "scripts.html";
include "footer.html"; 
?>
