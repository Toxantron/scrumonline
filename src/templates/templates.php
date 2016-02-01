<?php
/* 
 * Class representing a template
 */
class Template
{
  private $name;
  
  private $path;
  
  function __construct($name, $path)
  {
    $this->name = $name;
    $this->path = $path;
  }
  
  public function toTag()
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
      new Template("list.html", "templates/list.html"),
      new Template("master.html", "templates/master.php"),
      new Template("member.html", "templates/member.php"),
      new Template("404.html", "templates/404.html")
    ];
    return $templates;
  }
}