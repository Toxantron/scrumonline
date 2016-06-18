<?php
/*
 * Controller to create session statistics
 */
class StatisticsController extends ControllerBase
{
    private function sessionStatistics($sessionId)
    {
        $session = $this->getSession($sessionId);
        return $session->getPolls();
    }
    
    public function execute()
    {
        return $this->sessionStatistics($_GET["id"]);
    }
}

$controller = new StatisticsController($entityManager);
?>