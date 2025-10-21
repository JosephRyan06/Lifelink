<?php
$host = "localhost";   
$user = "root";        
$pass = "josephryan2006";            
$dbname = "db_lifelink"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
