<?php
$navItems = [
  [ 
    "link" => "\"/sessions/list.php\"", 
    "name" => "Sessions"
  ],
  
];
?>

<!doctype html>
<html class="no-js" lang="en-EN">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Online planning poker</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="apple-touch-icon" href="apple-touch-icon.png">
  <!-- Place favicon.ico in the root directory -->

  <!-- Boiler plate style sheets -->
  <link rel="stylesheet" href="/css/normalize.css">
  <link rel="stylesheet" href="/css/main.css">
  <script src="/js/vendor/modernizr-2.8.3.min.js"></script>

  <!-- Bootstrap style sheets -->
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/bootstrap-theme.min.css">

  <!--Custom styles -->
  <link rel="stylesheet" href="/css/scrumonline.css">
</head>
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
<?php foreach($navItems as $navItem): ?>
        <li<?php echo (isset($active) && $navItem["name"] === $active ? " class=\"active\"" : "") ?>><a href=<?php echo $navItem["link"] ?>><?php echo $navItem["name"] ?></a></li>
<?php endforeach; ?>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>

<!-- Add your site or application content here -->
<div id="view" class="container-fluid main" data-ng-app="scrum-online">
