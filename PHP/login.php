<?php
require_once('db.php');
session_start();

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Validate input
  if (empty($username) || empty($password)) {
    $errorMsg = "All fields are required.";
  } else {
    $pdo = getPDO();
    if (!$pdo) {
      $errorMsg = "Failed to connect to the database.";
    } else {
      try {
        // Check if username exists
        $sql = "SELECT id, password FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
          // Password is correct, start a session
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $username;
          header('Location: ../GUI/index.html');
          exit;
        } else {
          $errorMsg = "Invalid username or password.";
        }
      } catch (PDOException $e) {
        $errorMsg = "Error: " . $e->getMessage();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="../CSS/loginstyle.css">
</head>

<body>
  <div class="login-container">
    <div class="login-box">
      <h2>Login</h2>
      <?php if ($errorMsg) : ?>
        <div class="msg"><?php echo $errorMsg; ?></div>
      <?php endif; ?>
      <form method="post" action="login.php">
        <div class="form-group">
          <input type="text" id="username" name="username" placeholder="USERNAME" required>
        </div>
        <div class="form-group">
          <input type="password" id="password" name="password" placeholder="PASSWORD" required>
        </div>
        <button type="submit">Submit</button>
        <div class="register">
          <p>Don't have an account? <a href="signup.php">Create one</a></p>
        </div>
      </form>
    </div>
  </div>
</body>

</html>