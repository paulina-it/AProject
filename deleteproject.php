<?php
session_start();
require_once('connectdb.php');
$pid = $_GET['id'];

try {
    echo $pid;
    $statement = $db->prepare('DELETE FROM projects WHERE pid = ?');
    $statement->execute(array($pid));

    header("Location: personal.php");
    exit;
} catch (PDOexception $ex) {
    echo "Sorry, a database error occurred! <br>";
    echo "Error details: <em>" . $ex->getMessage() . "</em>";
}
?>