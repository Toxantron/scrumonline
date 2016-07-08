<?php
/*
 * Average time for an estimation from starting the poll to the 
 * final vote.
 */
class EstimationTime implements IStatistic
{
  public function getName()
  {
    return "EstimationTime";
  }
  
  public function getType()
  {
    return "time";
  }
  
  public function evaluate($session)
  {
    // Iterate over polls and calculate average time
    $total = 0.0;
    $count = 0;
    foreach($session->getPolls() as $poll) {
      $total += $poll->getEndTime()->diff($poll->getStartTime())->s;
      $count++;
    }
    return $total / $count;
  }
}

return new EstimationTime();
