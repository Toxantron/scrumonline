<?php
// Class for frequency of a certain card value
class CardFrequency
{
  function __construct($value)
  {
    $this->value = $value;
    $this->count = 0;
  }
  
  // Value of the card
  public $value;
  
  // Number of votes in poll
  public $count;
}