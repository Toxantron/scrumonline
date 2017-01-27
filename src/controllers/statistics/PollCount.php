<?php
/*
 * Number of polls in the session
 */
class PollCount implements IStatistic
{
  public function getType()
  {
    return "numeric";
  }
  
  public function evaluate($session)
  {
    return $session->getPolls()->count();
  }
}

return new PollCount();
