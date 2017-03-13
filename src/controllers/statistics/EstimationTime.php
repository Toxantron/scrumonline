<?php
/*
 * Average time for an estimation from starting the poll to the 
 * final vote.
 */
class EstimationTime implements IStatistic
{
  public function getType()
  {
    return "time";
  }
  
  public function evaluate($session)
  {
    // Iterate over polls and calculate average time
    $times = [];
    foreach($session->getPolls() as $poll) {
      $times[] = $poll->getEndTime()->diff($poll->getStartTime())->s;
    }
    return array_sum($times) / count($times);
  }
}

return new EstimationTime();
