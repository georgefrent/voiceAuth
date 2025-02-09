<!DOCTYPE html>
<html>
  <head>
    <title>Acasă</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">

  </head>
  <body>
      <div class="content-wrapper">
          <!-- Navbar -->
          <?php include('header.php'); ?>

          <!-- Main Content -->
          <div class="content container mt-4">
              <div class="row">
                  <!-- Left Column -->
                  <div class="col-md-8">
                  <?php if (isset($_SESSION['username'])): ?>
                    <h1>Bun venit, <span class="bold"><?php echo $_SESSION['username']; ?></span>!</h1>
                  <?php else: ?>
                    <h1>Bun venit!</h1>
                    <p>Aveti deja cont? Autentificați-vă!</p>
                    <a href="login.php">
                    <button type="submit" class="btn btn-primary">Autentificare</button>
                    </a>
                  <?php endif; ?>     
                  
                  </div>

                  <!-- Right Sidebar -->
                  <?php if (!isset($_SESSION['username'])): ?>
                  <div class="col-md-4">
                    <h2>Înregistrare</h2>
                    <form method="POST" action="register.php">
                        <div class="form-group">
                            <label for="username">Nume utilizator</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Parolă</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Înregistrare</button>
                    </form>
                  </div>
                  <?php endif; ?>
                  
              </div>
          </div>

          <!-- Footer -->
          <?php include('footer.php'); ?>
      </div>

      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>

<?php

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
if (isset($_SESSION['username']) && $voiceFile == 0) {
  header('Location: voiceRegister.php');
  exit();
} else if (isset($_SESSION['username']) && $authToken == 0) {
  header('Location: voiceRec.php');
  exit();
}

?>