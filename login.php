<?php
session_start();
require_once "db.php";

if(isset($_SESSION['username'])) {
    if($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: index.php');
        exit;
    }
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['pass'], $_POST['role'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $sql = "SELECT Id, username, password, role FROM login WHERE username = '$username' AND password = '$pass' AND role = '$role' LIMIT 1;";
    $result = mysqli_query($conn, $sql);

    if($row = mysqli_fetch_assoc($result)) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $row['Id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        // Əvvəl saxlanmış redirect varsa götür
        $redirect = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);

        if($redirect) {
            // əgər redirect admin.php-dir amma istifadəçi admin deyilsə -> index.php
            if(basename($redirect) === 'admin.php' && $_SESSION['role'] !== 'admin') {
                header('Location: index.php');
                exit;
            } else {
                header("Location: $redirect");
                exit;
            }
        } else {
            // default yönləndirmə role-a görə
            if($_SESSION['role'] === 'admin') {
                header('Location: admin.php');
                exit;
            } else {
                header('Location: index.php');
                exit;
            }
        }
    } else {
        $error = "Invalid username or password or role.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="CSS/login.css"/>
</head>
<body>
  <div class="page-bg">
    <form class="login-card" method="POST" action="">
      <h2 class="logo">Book<span>Worm</span></h2>
      <p class="subtitle">Welcome — please log in</p>

      <?php if($error): ?>
        <div style="color: red; margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <label class="sr-only" for="username">Username</label>
      <input id="username" type="text" name="username" placeholder="Username" required>

      <label class="sr-only" for="pass">Password</label>
      <input id="pass" type="password" name="pass" placeholder="Password" required>

      <!-- User/Admin seçim -->
      <label for="role" class="role-label">Select role:</label>
      <select id="role" name="role" class="role-select" required>
          <option value="user">User</option>
          <option value="admin">Admin</option>
      </select>

      <button type="submit" class="btn">Login</button>

      <div class="help-row">
          <label class="remember"><input type="checkbox" name="remember"> Remember me</label>
          <a class="forgot" href="#">Forgot password?</a>
      </div>
    </form>
  </div>
</body>
</html>
