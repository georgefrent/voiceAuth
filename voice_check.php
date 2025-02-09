<?php

session_start();
require('config.php');

$loggedInUsername = $_SESSION['username'];

$sql = "SELECT voiceFile FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUsername);
$stmt->execute();
$stmt->bind_result($voiceFile);
$stmt->fetch();
$stmt->close();

if ($voiceFile === 0) {

    header("Location: voiceRegister.php");

} else {

    header("Location: voiceRec.php");

}

?>