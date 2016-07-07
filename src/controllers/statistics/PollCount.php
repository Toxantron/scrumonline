<?php
/*
 * Number of polls in the session
 */
class PollCount implements IStatistic
{
  public function evaluate($session)
  {
    $result = new Statistic();
    $result->name = "Poll count";
    $result->type = "numeric";
    
    $result->value = $session->getPolls()->count();
    
    return $result;
  }
}

return new PollCount();
?>