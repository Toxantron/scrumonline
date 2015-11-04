<?php
$navItems = [
  [ 
    "link" => "\"sessions/list.php\"", 
    "name" => "Sessions"
  ],
  
];
?>

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
        <li><a<?php echo ($navItem["name"] === $active ? "class=\"active\"" : "") ?>  href=<?php echo $navItem["link"] ?>><?php echo $navItem["name"] ?></a></li>
<?php endforeach; ?>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>