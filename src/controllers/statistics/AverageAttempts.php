<?php
/*
 * TODO: Documentation for AverageAttempts
 */
class AverageAttempts implements IStatistic
{
  public function evaluate($session)
  {
    $result = new Statistic();
    $result->name = "AverageAttempts";
    $result->type = "numeric"; // Set type
    
    // Calculate attempts
    $attempts = [];
    foreach($session->getPolls() as $poll) {
      $id = $poll->getTopic();
      if(isset($attempts[$id])) {
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
    $result->value = $total / sizeof($attempts);
    
    return $result;
  }
}

return new AverageAttempts();
