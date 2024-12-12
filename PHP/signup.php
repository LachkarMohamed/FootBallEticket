<?php
require_once('db.php');

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $errorMsg = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $errorMsg = "Passwords do not match.";
    } else {
        $pdo = getPDO();
        if (!$pdo) {
            $errorMsg = "Failed to connect to the database.";
        } else {
            try {
    
                $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
                $stmt->execute([':username' => $username]);
                if ($stmt->fetch()) {
                    $errorMsg = "Username already exists.";
                } else {
                    
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                    // Insert the new user into the database
                    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':username' => $username, ':password' => $hashedPassword]);

                    header('Location: login.php');
                    exit;
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="../CSS/signup.css">
    <style>
        .msg {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-box">
            <h2>Sign Up</h2>
            <?php if ($errorMsg): ?>
                <div class="msg"><?php echo $errorMsg; ?></div>
            <?php endif; ?>
            <form method="post" action="signup.php">
                <div class="form-group">
                    <input type="text" id="new-username" name="username" placeholder="USERNAME" required>
                </div>
                <div class="form-group">
                    <input type="password" id="new-password" name="password" placeholder="PASSWORD" required>
                </div>
                <div class="form-group">
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="CONFIRM PASSWORD" required>
                </div>
                <button type="submit">Register</button>
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
