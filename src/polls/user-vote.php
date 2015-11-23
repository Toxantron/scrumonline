<?php
class UserVote
{
  private function __construct()
  {
    $this->placed = false;
    $this->flipped = false;
    $this->value = 0;
  }
  
  // Create instance from member entity
  public static function create($member, $currentPoll)
  {
    $vote = new UserVote();
    $vote->id = $member->getId();
    $vote->name = $member->getName();
    
    // Poll related values
    if(is_null($currentPoll))
      return $vote;
  
    // Flip cards if poll was completed
    $vote->flipped = $currentPoll->getResult() > 0;
    
    // Find matching member in poll
    foreach($currentPoll->getVotes() as $candidate)
    {
      if($candidate->getMember() === $member)
      {
        $match = $candidate; 
      }
    }
  
    if(isset($match))
    {
      $vote->placed = true;
      $vote->value = $match->getValue();  
    }
    
    return $vote;
  }
  
  // Id of the member
  public integer $id;
  
  // Name of the user
  public string $name;
  
  // Flag if value was set allready
  public bool $placed;
  
  // Value of the vote
  public float $value;
  
  // For ui only
  public bool $flipped;
}