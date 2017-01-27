<?php
/*
 * Average number of estimations attempts per topic /story
 */
class AverageAttempts implements IStatistic
{
  public function getType()
  {
    return "numeric";
  }
  
  public function evaluate($session)
  {
    // Calculate attempts
    $attempts = [];
    foreach($session->getPolls() as $poll) {
      $id = $poll->getTopic();
      if (isset($attempts[$id])) {
        $attempts[$id]++;
      } else {
        $attempts[$id] = 1;
      }
    }
    
    // Calculate average
    $total = 0;
    foreach($attempts as $pollAttempts) {
      $total += $pollAttempts;
    }
    return $total / sizeof($attempts);
  }
}

return new AverageAttempts();
