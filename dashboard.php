<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT id, name, email, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Dashboard | Simple Admin</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="vendors/bootstrap/css/bootstrap-4.3.1/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/simple-custom.css">
	<link rel="stylesheet" href="vendors/icon/mdi/css/materialdesignicons.min.css">
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-2">
				<div class="col-md-2 sidebar">
					<div class="brand">
						<a href="#" class="brand-name">Simple Admin</a>
					</div>
					<div class="sidebar-sticky">
						<ul class="nav flex-column">
							<li class="nav-item">
								<a href="dashboard.php" class="nav-link active"><i class="mdi mdi-monitor"></i> Dashboard</a>
							</li>
                            <li class="nav-item">
								<a href="register-users.php" class="nav-link active"><i class="mdi mdi-monitor"></i> Calon Pendaftar</a>
							</li>
                            <li class="nav-item">
								<a href="users.php" class="nav-link active"><i class="mdi mdi-monitor"></i> Calon Mahasiswa</a>
							</li>
                            <li class="nav-item">
								<a href="dashboard.php" class="nav-link active"><i class="mdi mdi-monitor"></i> Admin</a>
							</li>
							<!-- Other links -->
						</ul>
					</div>
					<div class="nav-bottom">
						<ul class="nav">
							<li class="nav-item"><a href="#" class="nav-link"><i class="mdi mdi-account"></i></a></li>
							<li class="nav-item"><a href="#" class="nav-link"><i class="mdi mdi-message"></i></a></li>
							<li class="nav-item"><a href="#" class="nav-link"><i class="mdi mdi-bell"></i></a></li>
							<li class="nav-item"><a href="logout.php" class="nav-link"><i class="mdi mdi-power"></i></a></li>
						</ul>
					</div>
				</div>
			</div>

			<main class="col-md-9 col-lg-10" role="main">
				<div class="container">
					<div class="content-header mt-2 mb-3">
						<h2 class="text-header">Dashboard</h2>
					</div>
					<div class="alert alert-danger">
						This template is under maintenance!
					</div>

					<!-- Widgets (same as before) -->
					<div class="row mb-3 widget">
						<!-- Your four widget cards -->
					</div>

					<!-- Replace "Chart Daily" with Registered Users -->
					<div class="row basic mb-3">
						<div class="col-md-6 pr-1">
							<div class="card">
								<div class="card-body">
									<label class="title-body">Calon Pendaftar</label>
									<div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
										<table class="table table-sm table-bordered mb-0">
											<thead class="thead-dark">
												<tr>
													<th>ID</th>
													<th>Name</th>
													<th>Email</th>
													<th>Registered At</th>
												</tr>
											</thead>
											<tbody>
												<?php if ($result && $result->num_rows > 0): ?>
													<?php while ($row = $result->fetch_assoc()): ?>
														<tr>
															<td><?= htmlspecialchars($row['id']) ?></td>
															<td><?= htmlspecialchars($row['name']) ?></td>
															<td><?= htmlspecialchars($row['email']) ?></td>
															<td><?= htmlspecialchars($row['created_at']) ?></td>
														</tr>
													<?php endwhile; ?>
												<?php else: ?>
													<tr><td colspan="4" class="text-center">No users found.</td></tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>

						<!-- Todo List remains unchanged -->
						<div class="col-md-6">
							<div class="card">
								<div class="card-body">
									<label class="title-body">Calon Mahasiswa</label>
									<canvas style="width: 100%" height="140"></canvas>
								</div>
							</div>
						</div>
					</div>

				</div>
			</main>
		</div>
	</div>

<!-- Scripts -->
<script src="vendors/bootstrap/js/jquery-3.3.1/jquery-3.3.1.min.js"></script>
<script src="vendors/bootstrap/js/bootstrap-4.3.1/bootstrap.min.js"></script>
<script src="vendors/bootstrap/js/popper/popper.min.js"></script>
</body>
</html>
