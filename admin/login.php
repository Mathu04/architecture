<?php
session_start();
include "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="../img/icons/icon-1.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #000, #020617);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .signin-card {
            background: #fff;
            border-radius: 14px;
            padding: 35px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 40px rgba(0,0,0,.25);
        }
        .btn-signin {
            background-color: #dfa974;
            border: none;
            height: 48px;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-signin:hover {
            background-color: #c9925e;
        }
    </style>
</head>

<body>

<div class="signin-card">

    <div class="text-center mb-3">
         <a href="../index.html">
        <img src="../img/icons/icon-1.png" width="70">
    </a>
    </div>

    <p class="text-center">Welcome back! Please login</p>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="xxx@gmail.com" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control"placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-signin w-100 text-white">
            Sign In
        </button>
        <div class="signup-link">
            Already have an account?
            <a href="../signup.html">Sign Up</a>
        </div>
    </form>

</div>

</body>
</html>
