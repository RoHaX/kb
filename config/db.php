<?php

$config = include('config.php');
$host = $config['host'];
$username = $config['user'];
$password = $config['password'];
$database = $config['database'];

$db  = new PDO("mysql:host=$host;dbname=$database", $username, $password);
			   
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("SET NAMES 'utf8';");
$db->exec("use `$database`");
