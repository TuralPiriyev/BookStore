<?php
  $dbhost = 'localhost';
  $dbuser = 'root';
  $dbpass = '';
  $dbname = 'bookstore';

  $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

  if(!$conn) echo "ugursuz!";
?>