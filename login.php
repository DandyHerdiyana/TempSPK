<?php
include "db.php";
session_start();

$error = "";

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
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Login</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="vendors/bootstrap/css/bootstrap-4.3.1/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/simple-custom.css">
	<style>
		.login-body {
			background-color: #5dade2;
			min-height: 100vh;
			display: flex;
			align-items: center;
		}
		.a-card {
			box-shadow: 0 4px 20px rgba(0,0,0,0.1);
			border-radius: 1rem;
		}
		.btn-water-mid {
			background-color: #2980b9;
			color: white;
		}
	</style>
</head>
<body class="bg-water-mid login-body">

	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8 col-lg-5">
				<h2 class="text-center mb-4 text-white">Welcome Back</h2>
				<div class="card a-card">
					<div class="card-body">
						<?php if (!empty($error)): ?>
							<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<strong>Error:</strong> <?= htmlspecialchars($error) ?>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<?php endif; ?>

						<form method="POST" action="">
							<div class="form-group">
								<label for="email">Email address</label>
								<input type="email" class="form-control" name="email" id="email" placeholder="Enter email" required>
							</div>
							<div class="form-group">
								<label for="password">Password</label>
								<input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
							</div>
							<div class="form-group form-check d-flex justify-content-between align-items-center">
								<div>
									<input type="checkbox" class="form-check-input" id="rememberme" name="rememberme">
									<label class="form-check-label" for="rememberme">Remember Me</label>
								</div>
								<a href="#" class="text-water-mid small">Forgot Password?</a>
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-water-mid btn-block">Login</button>
							</div>
						</form>
					</div>
				</div>
				<p class="text-center text-white mt-3">Fends &copy; 2019</p>
			</div>
		</div>
	</div>

	<script src="vendors/bootstrap/js/jquery-3.3.1/jquery-3.3.1.min.js"></script>
	<script src="vendors/bootstrap/js/bootstrap-4.3.1/bootstrap.min.js"></script>
	<script src="vendors/bootstrap/js/popper/pooper.min.js"></script>
</body>
</html>
