<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Fees Management System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .nav {
            background: #333;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        .nav a {
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            display: inline-block;
            transition: background 0.3s;
        }

        .nav a:hover {
            background: #555;
        }

        .content {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .logout-btn {
            background: #dc3545 !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        table th, table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin-bottom: 15px;
            color: #667eea;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h1>🎓 College Fees Management System</h1>
            <p>Efficient Fee Collection & Student Management</p>
        </div>

        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <div class="nav">
            <a href="../index.php">Dashboard</a>
            <a href="../students/add_student.php">Add Student</a>
            <a href="../students/view_students.php">View Students</a>
            <a href="../courses/manage_courses.php">Manage Courses</a>
            <a href="../courses/view_fees_structure.php">Fee Structure</a>
            <a href="../payments/make_payment.php">Make Payment</a>
            <a href="../payments/view_payments.php">View Payments</a>
            <a href="../admin/logout.php" class="logout-btn">Logout</a>
        </div>
        <?php endif; ?>

        <div class="content">
