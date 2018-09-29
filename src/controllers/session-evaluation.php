<?php 
/*
 * Shared function to evaluate sessions
 */
class SessionEvaluation
{
    // Evaluate the polls average
  public static function evaluatePoll($session, $currentPoll)
  {
    $sum = 0;
    $count = $currentPoll->getVotes()->count();
    
    if($count <= 0 || $count != $session->getMembers()->count())
      return false;
    
    foreach($currentPoll->getVotes() as $vote)
    {
      $sum += $vote->getValue();  
    }
    $currentPoll->setResult($sum / $count); 
    $currentPoll->setEndTime(new DateTime());

    return true;
  }
  
  // Highlight highest and lowest estimate
  public static function highlightVotes($session, $currentPoll, $cardSet)
  {
    include __DIR__ . "/card-frequency.php";
    
    $votes = $currentPoll->getVotes();
    // Frequency for each card
    $frequencies = [];
    foreach($cardSet as $key=>$card)
    {
      $frequencies[$key] = new CardFrequency($key); 
    }
    
    // Count absolute frequence
    foreach($votes as $vote)
    {
      $frequencies[$vote->getValue()]->count++;
    }
  
    // Determine most common vote
    foreach($frequencies as $frequency)
    {
      if(!isset($mostCommon) || $mostCommon->count < $frequency->count)
        $mostCommon = $frequency;
    }
    
    $min = 0; $max = 0;
    // Iterate over frequencies and find lowest
    foreach($frequencies as $frequency)
    {
      $min = self::selectLimits($votes, $frequency, $mostCommon);
      if($min != 0) 
        break;
    }
    // Iterate over frequencies and find highest
    foreach(array_reverse($frequencies) as $frequency)
    {
      $max = self::selectLimits($votes, $frequency, $mostCommon);
      if($max != 0)
        break;
    }
    
    $currentPoll->setConsensus($min == -1 && $max == -1);
  }
  
  // Select highest or lowest estimates -  depends on direction of loop
  private static function selectLimits($votes, $frequency, $mostCommon)
  {
    // No card at all
    if($frequency->count == 0)
      return 0;
    // This is the most common, no lowest found
    if($frequency == $mostCommon)
      return -1;
    
    foreach($votes as $vote)
    {
      if($vote->getValue() == $frequency->value)
        $vote->setHighlighted(true);
    }
    return 1;
  }
}