<?php 
  $conn= new mysqli('localhost','root','','enterprise_db');
  if (!$conn)
  die("Could not connect to mysql".mysqli_error($con));
?>