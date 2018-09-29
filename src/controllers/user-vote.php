<?php
class UserVote
{
  private function __construct()
  {
    $this->placed = false;
    $this->value = 0;
    $this->active = false;
  }
  
  // Create vote object from query object
  public static function fromQuery($cardSet, $entity)
  {
    $vote = new UserVote();
    $vote->id = $entity['id'];
    $vote->name = $entity['name'];
    if($entity['value'] !== null)
    {
      $vote->placed = true;
      $vote->value = $cardSet[$entity['value']];  
      $vote->active = $entity['highlighted'];
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

  // Flag if the requesting user has the rights to delete this user (and its vote)
  public $canDelete;
}