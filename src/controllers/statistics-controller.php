<?php
require_once __DIR__ . "/statistic.php";
/*
 * Controller to create session statistics
 */ 
class StatisticsController extends ControllerBase
{
    private static function loadPlugins()
    {
      $plugins = [];
      foreach(glob(__DIR__ . '/statistics/*.php') as $file) {
        // Check if the plugin was selected in the filter
        $key = basename($file, ".php");
        $plugins[$key] = include $file;
      }
      return $plugins;
    }
    
    public function calculate($id)
    {
      // Id and session entity
      $session = $this->getSession($id);
      
      // Evaluation
      $statistics = [];
      foreach ($this->loadPlugins() as $key => $plugin) {
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