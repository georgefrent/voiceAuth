<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $password, $email);


    if ($stmt->execute()) {
        echo "Ați fost înregistrat cu succes! <a href='login.php'>Autentificați-vă aici.</a>.";
    } else {
        echo "Eroare: Nu am putut înregistra utilizatorul.";
    }

    $stmt->close();
}
?>
