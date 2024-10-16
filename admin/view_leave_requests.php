<?php
require "config.php";
session_start();

// Check for delete message in session
if (isset($_SESSION["delete"])) {
    unset($_SESSION["delete"]);
}

// Handle form submission to update leave request status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $leave_id = $_POST['leave_id'];
    $status = $_POST['status'];

    // Prepare the SQL statement to update the leave request status
    $sql = "UPDATE leave_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // Bind the parameters (status as string, leave_id as integer)
    $stmt->bind_param("si", $status, $leave_id);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Redirect back to the leave requests page upon success
        header("Location: view_leave_requests.php");
        exit(); // Ensure no further code is executed after redirect
    } else {
        // Handle error
        echo "Error: " . $stmt->error;
    }
}

// Fetch leave requests
$sql = "SELECT lr.id, e.name, lr.start_date, lr.end_date, lr.reason, lr.status 
        FROM leave_requests lr 
        JOIN employee e ON lr.employee_id = e.id 
        ORDER BY lr.start_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Requests</title>
    <link rel="icon" href="../HRMS_LOGO.png" type="image/png">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
    #logo {
        height: 12vmin;
        width: 30vmin;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <i class="nav-icon fas fa-user-tie"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Setting</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <?php require "./sidebar.php"; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Leave Requests</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body mb-5">
                                    <form action="" method="POST" class="text-center">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Employee Name</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Reason</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= $count++ ?></td>
                                                        <td><?= htmlspecialchars($row['name']) ?></td>
                                                        <td><?= htmlspecialchars($row['start_date']) ?></td>
                                                        <td><?= htmlspecialchars($row['end_date']) ?></td>
                                                        <td><?= htmlspecialchars($row['reason']) ?></td>
                                                        <td><?= htmlspecialchars($row['status']) ?></td>
                                                        <td>
                                                            <form action='process_leave.php' method='POST'>
                                                                <input type='hidden' name='leave_id'
                                                                    value='<?= htmlspecialchars($row['id']) ?>'>
                                                                <select name='status' required>
                                                                    <option value='Approved'>Approve</option>
                                                                    <option value='Rejected'>Reject</option>
                                                                </select>
                                                                <input type='submit' value='Update Status'
                                                                    class="btn btn-primary">
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; Human Resource Management System 2024-25.</strong> All rights reserved.
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>