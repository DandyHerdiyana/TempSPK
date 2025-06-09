<?php
include "db.php";
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch all users with extra columns
$sql = "SELECT id, name, email, nidn, role, accessibility, approved, created_at 
	FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

// Count and latest user name
$count = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
$latest = $conn->query("SELECT name FROM users ORDER BY created_at DESC LIMIT 1")->fetch_assoc()["name"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>All Registered Users</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="vendors/bootstrap/css/bootstrap-4.3.1/bootstrap.min.css">
	<link rel="stylesheet" href="vendors/icon/mdi/css/materialdesignicons.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="users.css">
	<style>
		body {
			background-color: #f5f7fa;
		}
		.page-header {
			margin-top: 20px;
			margin-bottom: 20px;
		}
		.table th, .table td {
			vertical-align: middle;
			font-size: 14px;
		}
	</style>
</head>
<body>

<div class="container-fluid">
	<div class="page-header d-flex justify-content-between align-items-center">
		<h2>All Registered Users</h2>
		<a href="dashboard.php" class="btn btn-secondary btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Dashboard</a>
	</div>

	<div class="row mb-4">
		<div class="col-md-6">
			<div class="alert alert-primary"><strong>Total Users:</strong> <?= $count ?></div>
		</div>
		<div class="col-md-6">
			<div class="alert alert-info"><strong>Latest Registered:</strong> <?= htmlspecialchars($latest) ?></div>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<div class="d-flex justify-content-between mb-3">
				<h4 class="card-title mb-0">User List</h4>
				<button id="exportBtn" class="btn btn-sm btn-success"><i class="mdi mdi-download"></i> Export CSV</button>
			</div>
			<div class="table-responsive">
				<table id="userTable" class="table table-bordered table-hover table-sm">
					<thead class="thead-dark">
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Email</th>
							<th>NIDN</th>							
							<th>Role</th>
							<th>Access</th>
							<th>Approved</th>
							<th>Registered</th>
							<th></th>							
						</tr>
					</thead>
					<tbody>
						<?php while ($row = $result->fetch_assoc()): ?>
							<tr>
								<td><?= htmlspecialchars($row["id"]) ?></td>
								<td><?= htmlspecialchars($row["name"]) ?></td>
								<td><?= htmlspecialchars($row["email"]) ?></td>
								<td><?= htmlspecialchars($row["nidn"]) ?></td>								
								<td><?= htmlspecialchars($row["role"]) ?></td>
								<td>
								<?php
									$akses = [
									'full' => 'Penuh',
									'limited' => 'Terbatas',
									'restricted' => 'Dibatasi'
									];
									echo $akses[$row["accessibility"]] ?? 'Tidak diketahui';
								?>
								</td>
								<td><?= $row["approved"] ? 'Yes' : 'No' ?></td>
								<td><?= htmlspecialchars($row["created_at"]) ?></td>

								<!-- Profile Link -->
								<td>
								<a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Scripts -->
<script src="vendors/bootstrap/js/jquery-3.3.1/jquery-3.3.1.min.js"></script>
<script src="vendors/bootstrap/js/bootstrap-4.3.1/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
	const table = $('#userTable').DataTable({
		"order": [[10, "desc"]],
		"pageLength": 10
	});

	// Export to CSV
	$('#exportBtn').click(function() {
		let csv = 'ID,Name,Email,Role,Access,Approved,Birthdate,Origin,Wave,Graduation,Registered At\n';
		table.rows({ search: 'applied' }).every(function() {
			let data = this.data().map(d => {
				if (typeof d === 'string') {
					return '"' + d.replace(/"/g, '""') + '"';
				}
				return d;
			});
			csv += data.join(',') + "\n";
		});

		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		const link = document.createElement("a");
		link.href = URL.createObjectURL(blob);
		link.download = "registered_users.csv";
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
	});
});
</script>
</body>
</html>
