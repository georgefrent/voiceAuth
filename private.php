<!DOCTYPE html>
<html>
  <head>
    <title>Pagină privată</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">

  </head>
  <body>
    <div class="content-wrapper">
      <?php include('header.php'); ?>
      <div class="content container mt-4">
        <h2>Pagină privată</h2>
        <p>Bun venit, <?php echo $_SESSION['username']; ?>!</p>
        <a href="logout.php" class="btn btn-primary mr-5">Deconectare</a>
        <a href="deleteAcc.php" class="btn btn-danger">Șterge cont</a>
      </div>
      <?php include('footer.php'); ?>
    </div>

      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

<?php

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

require('config.php');
$username = $_SESSION['username'];

$query = "SELECT voiceFile, authToken FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if($row = $result->fetch_assoc()) 
{
  $voiceFile = $row['voiceFile'];
  $authToken = $row['authToken'];
}
if ($voiceFile == 0) {
  header('Location: voiceRegister.php');
  exit();
} else if ($authToken == 0) {
  header('Location: voiceRec.php');
  exit();
}

?>