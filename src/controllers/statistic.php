<?php
/*
 * Interface for statistics plugins
 */
interface IStatistic
{
  /*
   * Evaluate the session
   */
  function evaluate($session);
} 

class Statistic
{
  // Name of the statistic
  public $name; 
  
  // Value of the statistic
  public $value;
  
  // Visualization of the statistic, e.g numeric, percent, etc.
  public $type;
}
?>