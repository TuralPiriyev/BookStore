<?php
   require_once "db.php";
   if(isset($_POST['username']) && isset($_POST['pass']) && isset($_POST['role']))
   {
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $pass = mysqli_real_escape_string($conn,$_POST['pass']);
    $role = mysqli_real_escape_string($conn,$_POST['role']);

    $sql = "Select username, password, role from login where username = '$username' and password = '$pass' and role = '$role'";
    $result = mysqli_query($conn, $sql);
     if(mysqli_num_rows($result)>0)
     { 
        if($role === 'admin'){header("Location: admin.php"); exit;}
        else{header("Location: index.php"); exit;}
       
     }
     else{echo "Invalid username or password or role";}
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
