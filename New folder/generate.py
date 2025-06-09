import os
from zipfile import ZipFile

# Folder setup
base_folder = "assignment_dashboard"
os.makedirs(base_folder, exist_ok=True)

files = {
    "db.php": '''<?php
$conn = new mysqli("localhost", "root", "", "assignment_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>''',

    "register.php": '''<?php
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
</div></body></html>''',

    "login.php": '''<?php
include "db.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: dashboard.php");
    } else {
        echo "<p class='error'>Invalid email or password.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title>Login</title></head>
<body><div class="container">
<h2>Login</h2>
<form method="post">
<input type="email" name="email" placeholder="Email" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<input type="submit" value="Login">
</form>
<p>Don't have an account? <a href="register.php">Register here</a>.</p>
</div></body></html>''',

    "dashboard.php": '''<?php
session_start();
include "db.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$result = $conn->query("SELECT id, name, email, created_at FROM users");
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"><title>Dashboard</title></head>
<body><div class="container">
<h2>User Dashboard</h2>
<p><a href="logout.php">Logout</a></p>
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Registered At</th></tr>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row["id"] ?></td>
<td><?= $row["name"] ?></td>
<td><?= $row["email"] ?></td>
<td><?= $row["created_at"] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div></body></html>''',

    "logout.php": '''<?php
session_start();
session_destroy();
header("Location: login.php");
?>''',

    "style.css": '''body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}
.container {
    width: 400px;
    margin: 50px auto;
    padding: 20px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
}
input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px;
    width: 100%;
    border: none;
    cursor: pointer;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    border: 1px solid #ddd;
    padding: 8px;
}
th {
    background-color: #f2f2f2;
}
a {
    color: #4CAF50;
    text-decoration: none;
}
.error {
    color: red;
}'''
}

# Write files and zip them
for filename, content in files.items():
    with open(os.path.join(base_folder, filename), 'w', encoding='utf-8') as f:
        f.write(content)

with ZipFile(f"{base_folder}.zip", 'w') as zipf:
    for filename in files.keys():
        zipf.write(os.path.join(base_folder, filename), arcname=os.path.join(base_folder, filename))

print("✅ ZIP file created successfully: assignment_dashboard.zip")
