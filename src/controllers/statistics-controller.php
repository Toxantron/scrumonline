<?php
require_once __DIR__ . "/statistic.php";
/*
 * Controller to create session statistics
 */ 
class StatisticsController extends ControllerBase
{
    private function loadPlugins($filter)
    {
      $plugins = [];
      foreach(glob(__DIR__ . '/statistics/*.php') as $file) {
        // Check if the plugin was selected in the filter
        $key = basename($file, ".php");
        if($filter == null || in_array($key, $filter)) {
          $plugin = include $file;
          $plugins[$plugin->getName()] = $plugin; 
        }
      }
      return $plugins;
    }
    
    public function execute()
    {
        // Id and session entity
        $id = $_GET["id"];
        $session = $this->getSession($id);
        
        // Optional filter
        $filter = isset($_GET["filter"]) ? explode("|", $_GET["filter"]) : null;
        
        // Evaluation
        $statistics = [];
        foreach ($this->loadPlugins($filter) as $key => $plugin) {
          $statistic = new Statistic();
          $statistic->name = $key;
          $statistic->type = $plugin->getType();
          $statistic->value = $plugin->evaluate($session);
          
          $statistics[] = $statistic;
        }
        
        return $statistics;
    }
}

return new StatisticsController($entityManager);
?>