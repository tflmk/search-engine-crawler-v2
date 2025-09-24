<?php 

     $hostname = 'localhost';
     $username = 'websurfed_search';
     $password = 'wjgt2d7a86h77MaN8y9P';
     $db_name = 'websurfed_search';

$conn = mysqli_connect($hostname, $username, $password, $db_name);

if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

?>