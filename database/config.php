<?php
$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = '';
$dbname = 'skillswaps';

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

session_start();
