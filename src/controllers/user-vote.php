<?php
class UserVote
{
  private function __construct()
  {
    $this->placed = false;
    $this->value = 0;
    $this->active = false;
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
  public $id;
  
  // Name of the user
  public $name;
  
  // Flag if value was set allready
  public $placed;
  
  // Value of the vote
  public $value;
  
  // Member must explain his vote
  public $active;
}

// Class for frequency of a certain card value
class CardFrequency
{
  function CardFrequency($value)
  {
    $this->value = $value;
    $this->count = 0;
  }
  
  // Value of the card
  public $value;
  
  // Number of votes in poll
  public $count;
}