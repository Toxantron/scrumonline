<!doctype html>
<html class="no-js" lang="en-EN">
    <?php include("header.html"); ?>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        
        <!-- Top navigation bar -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="/">Planning poker</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
              <ul class="nav navbar-nav">
                <li class="active"><a href="/">Home</a></li>
                <li><a href="sessions.html">Sessions</a></li>
              </ul>
            </div><!--/.nav-collapse -->
          </div>
        </nav>

        <!-- Add your site or application content here -->
        <div class="container-fluid main" >
          <div class="row">
            <div class="col-lg-1 col-lg-offset-4
                        col-md-2 col-md-offset-3
                        col-xs-4 col-xs-offset-4">
              <a class="btn btn-lg btn-default">Create session</a>
            </div>
            <div class="col-lg-1 col-lg-offset-2 col-md-2 col-md-offset-2 col-xs-4 col-xs-offset-4">
              <a class="btn btn-lg btn-default">Join session</a>
            </div>
          </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
