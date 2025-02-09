<!DOCTYPE html>
<html>
  <head>
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">

  </head>
  <body>
      <div class="content-wrapper">

          <?php include('header.php'); ?>

          <div class="content container mt-4">
            <h2>Pagină autentificare</h2>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form method="post">
              <div class="form-group">
                <label for="username">Utilizator:</label>
                <input type="text" name="username" class="form-control" required>
              </div>
              <div class="form-group">
                <label for="password">Parolă:</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary">Autentificare</button>
            </form>
          </div>
          <?php include('footer.php'); ?>
      </div>

      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

<?php

  // Redirect to the private page if the user is already authenticated
  if (isset($_SESSION['username'])) {
    header("Location: private.php");
    exit();
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $username = $_POST['username'];
      $password = md5($_POST['password']);

      $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
      $result = $conn->query($sql);
      
      if ($result->num_rows == 1) {
          $_SESSION['username'] = $username;
          header('Location: voice_check.php');
          exit();
      } else {
        echo '<script>alert("Nume de utilizator sau parolă greșită.")</script>';
      }
  }
?>
