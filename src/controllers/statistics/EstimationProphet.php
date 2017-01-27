<?php
/*
 * Name of the member whose first estimation most often matches
 * the final result. Polls with consensus on the first attempt
 * are not considered in the evaluation.
 */
class EstimationProphet implements IStatistic
{
  public function getType()
  {
    return "nominal"; // Define type
  }
  
  // Calculate the number of times each member was right the
  // first time
  private function calculatePoints(&$members, $polls)
  {
    $firstPoll = null;
    foreach ($polls as $poll) {
      // Save reference to the first attempt
      if ($firstPoll == null || $firstPoll->getTopic() != $poll->getTopic()) {
        $firstPoll = $poll;
        continue;
      }
        
      // Now only look at consensus
      if (!$poll->getConsensus())
        continue;
        
      // Compare first estimation and final results
      $result = $poll->getResult();    
      foreach ($firstPoll->getVotes() as $vote) {
        if ($vote->getValue() == $result) {
          // Award point to the member who was right
          $members[$vote->getMember()->getName()]++;
        }
      }
    }   
  }

  public function evaluate($session)
  {
    // Create dictionary of member and points
    $members = [];
    foreach ($session->getMembers() as $member) {
      $members[$member->getName()] = 0;
    }
    
    // Loop over polls and compare first estimation with consensus
    $this->calculatePoints($members, $session->getPolls());
    
    // Find member who was right most often
    $maxPoints = 0;
    $prophet = null;
    foreach ($members as $member => $points) {
      if ($points <= $maxPoints)
        continue;
        
      $prophet = $member;
      $maxPoints = $points;
    }
    
    return $prophet;
  }
}

return new EstimationProphet();
