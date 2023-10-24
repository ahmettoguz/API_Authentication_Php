<?php

$host = "localhost";
$databaseName = "api";
$user = "root";
$pass = "root";

// create data source name and connect to database
$dsn = "mysql:host=$host;dbname=$databaseName;charset=utf8mb4";

try {
    $db = new PDO($dsn, $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Database Connection Error !!!\n" . $e);
}
