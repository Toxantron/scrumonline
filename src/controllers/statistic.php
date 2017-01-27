<?php
/*
 * Interface for statistics plugins
 */
interface IStatistic
{
  /*
   * Type of result, e.g. numeric, time, nominal
   */
  function getType();
  
  /*
   * Evaluate the session
   */
  function evaluate($session);
} 

class Statistic
{
  // Name of the statistic
  public $name; 
  
  // Visualization of the statistic, e.g numeric, percent, etc.
  public $type;
  
  // Value of the statistic
  public $value = 0;
}
?>