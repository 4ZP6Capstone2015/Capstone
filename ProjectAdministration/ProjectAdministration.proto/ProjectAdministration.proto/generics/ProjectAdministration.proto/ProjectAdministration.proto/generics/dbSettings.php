<?php

global $DB_host,$DB_user,$DB_pass;
$DB_host='localhost';
$DB_user='ampersand';
$DB_pass='ampersand';

$DB_link=mysqli_connect($DB_host, $DB_user, $DB_pass)
      or exit("Error connecting to the database: username / password are probably incorrect.");

$sql="SET SESSION sql_mode = 'ANSI,TRADITIONAL'";
if (!mysqli_query($DB_link,$sql)) {
  die("Error setting sql_mode: " . mysqli_error($DB_link));
  }

?>
