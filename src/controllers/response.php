<?php
// Wrapper class for a boolean server response
class BoolResponse
{  
  // Indicator if operation was successful
  public $success; 

  function __construct($value = false)
  {
    $this->success = $value;
  }
}

// Wrapper class for a numeric server response
class NumericResponse
{
  // Value of the numeric response
  public $value;

  function __construct($value = 0)
  {
    $this->value = $value;
  }
}