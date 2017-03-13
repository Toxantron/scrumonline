<?php
/*
 * This statistic calculates the average time between two polls of
 * the same topic. It can be an indicator of unevenly spread knowledge
 * within the team.
 */
class DiscussionTime implements IStatistic
{
  public function getType()
  {
    return "time"; // Define type
  }

  public function evaluate($session)
  {
    // Fetch all discussion times
    $times = [];
    foreach($session->getPolls() as $poll) {
      if (!isset($last) || $last->getTopic() !== $poll->getTopic()) {
        // If this is the first poll or a new topic, simply continue
        $last = $poll;
        continue;
      }

      // Calculate the time difference from this poll to the previous one
      $times[] = $poll->getStartTime()->diff($last->getStartTime())->s;
      $last = $poll;
    }

    // No times -> no average
    if (count($times) == 0)
      return 0;
    
    // Calculate average
    return array_sum($times) / count($times);
  }
}

return new DiscussionTime();
