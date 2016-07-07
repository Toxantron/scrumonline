<?php
require_once __DIR__ . "/statistic.php";
/*
 * Controller to create session statistics
 */ 
class StatisticsController extends ControllerBase
{
    private function loadPlugins()
    {
      $plugins = [];
      foreach(glob(__DIR__ . '/statistics/*.php') as $file) {
        $plugin = include $file;
        array_push($plugins, $plugin);
      }
      return $plugins;
    }
    
    public function execute()
    {
        $id = $_GET["id"];
        $session = $this->getSession($id);
        
        $statistics = [];
        foreach ($this->loadPlugins() as $index => $plugin) {
          $statistics[$index] = $plugin->evaluate($session);
        }
        return $statistics;
    }
}

return new StatisticsController($entityManager);
?>