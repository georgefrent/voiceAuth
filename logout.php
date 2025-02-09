<?php
session_start();
require('config.php');
$username = $_SESSION['username'];

$stmt = $conn->prepare("UPDATE users SET authToken = 0 WHERE username = ?");
$stmt->bind_param("s", $username);

if ($stmt->execute()) {
    $stmt->close();
    session_destroy();
    header('Location: index.php');
    exit();
} else {
    echo json_encode(["message" => "Baza de date NU a fost actualizată."]);
}

?>
