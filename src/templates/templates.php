<?php
/* 
 * Class representing a template
 */
class Template
{
  public $isNavigation = false;
  
  public $navigationTag;
  
  public $link;
  
  private $name;
  
  private $path;
  
  function __construct($name, $path, $navigationTag = null)
  {
    $this->name = $name;
    $this->path = $path;
    
    if($navigationTag != null)
    {
      $this->isNavigation = true;
      $this->navigationTag = $navigationTag;
      $this->link = "/" . lcfirst($navigationTag);
    }  
  }
  
  public function render()
  {
?>
<script type="text/ng-template" id="<?= $this->name ?>">
  <?php include $this->path; ?>
</script>    
<?php 
  }
  
  public static function getAll()
  {
    $templates = [
      new Template("home.html", "templates/home.php"),
      new Template("join.html", "templates/join.php"),
      new Template("list.html", "templates/list.html", "Sessions"),
      new Template("master.html", "templates/master.php"),
      new Template("default_source.html", "templates/default_source.html"),
      new Template("add_source.html", "templates/add_source.html"),
      new Template("member.html", "templates/member.php"),
      new Template("removal.html", "templates/removal.html"),
      new Template("404.html", "templates/404.html")
    ];
    return $templates;
  }
}
