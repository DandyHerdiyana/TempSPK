<?php
include "db.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        echo "<p class='error'>Error: " . $stmt->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title>Register</title></head>
<body><div class="container">
<h2>Register</h2>
<form method="post">
<input type="text" name="name" placeholder="Name" required><br>
<input type="email" name="email" placeholder="Email" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<input type="submit" value="Register">
</form>
<p>Already have an account? <a href="login.php">Login here</a>.</p>
</div></body></html>