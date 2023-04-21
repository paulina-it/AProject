<?php

$db_host = 'localhost';
$db_name = 'aproject';
$username = 'root';
$password = '';

try {
	$db = new PDO("mysql:dbname=$db_name;host=$db_host", $username, $password); 
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $ex) {
	echo("Failed to connect to the database.<br>");
	echo($ex->getMessage());
	exit;
}
?>