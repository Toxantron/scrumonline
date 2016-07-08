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
        $plugins[$plugin->getName()] = $plugin;
      }
      return $plugins;
    }
    
    public function execute()
    {
        $id = $_GET["id"];
        $session = $this->getSession($id);
        
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