<?php
$conn = new mysqli("localhost", "root", "", "architecture_db");
if ($conn->connect_error) {
  die("Database connection failed");
}
?>
