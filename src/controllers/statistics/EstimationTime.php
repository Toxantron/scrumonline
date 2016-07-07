<?php
/*
 * TODO: Documentation for EstimationTime
 */
class EstimationTime implements IStatistic
{
  public function evaluate($session)
  {
    $result = new Statistic();
    $result->name = "EstimationTime";
    $result->type = "time"; // Set type
    
    // Iterate over polls and calculate average time
    $total = 0.0;
    $count = 0;
    foreach($session->getPolls() as $poll) {
      $total += $poll->getEndTime()->diff($poll->getStartTime())->s;
      $count++;
    }
    $result->value = $total / $count;
    
    return $result;
  }
}

return new EstimationTime();
