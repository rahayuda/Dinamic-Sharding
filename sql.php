<?php
$host_name  = "localhost";
$port       = "3307"; 
$user_name  = "root";
$password   = "maria";
$database_A = "db_A";
$database_B = "db_B";

// Koneksi ke DB A
$con_A = mysqli_connect($host_name . ":" . $port, $user_name, $password, $database_A);
if (!$con_A) {
    die("Koneksi gagal ke DB A: " . mysqli_connect_error());
}

// Koneksi ke DB B
$con_B = mysqli_connect($host_name . ":" . $port, $user_name, $password, $database_B);
if (!$con_B) {
    die("Koneksi gagal ke DB B: " . mysqli_connect_error());
}
?>
