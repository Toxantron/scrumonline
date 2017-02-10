<?php
// database configuration parameters
$conn = array(
    'dbname' => 'scrum_online',
    'user' => 'user',
    'password' => 'password',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);
  
$host = "www.scrumpoker.online";
  
$ga = 'GOOGLE-ANALYTICS';
  
$cards = [
    // Standard fibonaci like series of values 
    ['1' => 1, '2' => 2, '3' => 3, '5' => 5, '8' => 8, '13' => 13, '20' => 20, '40' => 40, '100' => 100],
    // Special card set with '?' for unclear stories
    ['1' => 1, '2' => 2, '3' => 3, '5' => 5, '8' => 8, '13' => 13, '20' => 20, '40' => 40, '?' => 100],
    // Powers of two used by other teams
    ['0' => 0 ,'1' => 1, '2' => 2, '4' => 4, '8' => 8, '16' => 16, '32' => 32, '64' => 64],
    // Card set used to estimate hours as different fractions and multiples of a working day
    ['1' => 1, '2' => 2, '4' => 4, '8' => 8, '12' => 12, '16' => 16, '24' => 24, '32' => 32, '40' => 40],
    // Demonstration of the coffee cup card
    ['cup' => -100, '1' => 1, '2' => 2, '3' => 3, '5' => 5, '8' => 8, '13' => 13, '20' => 20, '?' => 100],
];

$src = "https://github.com/Toxantron/scrumonline/tree/master";