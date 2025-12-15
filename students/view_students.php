<?php
require_once '../config/db_connect.php';
include '../includes/header.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}

// Get all students with course and payment information
$students = fetch_all("
    SELECT 
        s.student_id,
        s.name,
        s.email,
        s.phone,
        c.course_name,
        s.admission_year,
        c.total_fees,
        COALESCE(SUM(p.amount_paid), 0) as total_paid,
        (c.total_fees - COALESCE(SUM(p.amount_paid), 0)) as balance_due
    FROM students s
    INNER JOIN courses c ON s.course_id = c.course_id
    LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
    GROUP BY s.student_id, s.name, s.email, s.phone, c.course_name, s.admission_year, c.total_fees
    ORDER BY s.student_id DESC
");
?>

<h2>👨‍🎓 All Students</h2>

<div style="margin-bottom: 20px;">
    <a href="add_student.php" class="btn">➕ Add New Student</a>
</div>

<div class="card">
    <?php if (count($students) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Course</th>
                <th>Year</th>
                <th>Total Fees</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo $student['student_id']; ?></td>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td><?php echo htmlspecialchars($student['phone']); ?></td>
                <td><?php echo htmlspecialchars($student['course_name']); ?></td>
                <td><?php echo $student['admission_year']; ?></td>
                <td>₹<?php echo number_format($student['total_fees'], 2); ?></td>
                <td>₹<?php echo number_format($student['total_paid'], 2); ?></td>
                <td>₹<?php echo number_format($student['balance_due'], 2); ?></td>
                <td>
                    <?php 
                    $percentage_paid = ($student['total_fees'] > 0) ? ($student['total_paid'] / $student['total_fees'] * 100) : 0;
                    if ($percentage_paid >= 100) {
                        echo '<span style="padding: 5px 10px; border-radius: 3px; background: #d4edda; color: #155724;">Paid</span>';
                    } elseif ($percentage_paid >= 50) {
                        echo '<span style="padding: 5px 10px; border-radius: 3px; background: #fff3cd; color: #856404;">Partial</span>';
                    } else {
                        echo '<span style="padding: 5px 10px; border-radius: 3px; background: #f8d7da; color: #721c24;">Pending</span>';
                    }
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No students found. <a href="add_student.php">Add your first student</a></p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>📊 Summary Statistics</h3>
    <?php
    $total_students = count($students);
    $total_expected = array_sum(array_column($students, 'total_fees'));
    $total_collected = array_sum(array_column($students, 'total_paid'));
    $total_pending = array_sum(array_column($students, 'balance_due'));
    $collection_rate = ($total_expected > 0) ? ($total_collected / $total_expected * 100) : 0;
    ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Total Students:</strong><br>
            <span style="font-size: 1.5em; color: #667eea;"><?php echo $total_students; ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Expected Revenue:</strong><br>
            <span style="font-size: 1.5em; color: #667eea;">₹<?php echo number_format($total_expected, 2); ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Collected:</strong><br>
            <span style="font-size: 1.5em; color: #43e97b;">₹<?php echo number_format($total_collected, 2); ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Pending:</strong><br>
            <span style="font-size: 1.5em; color: #f5576c;">₹<?php echo number_format($total_pending, 2); ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Collection Rate:</strong><br>
            <span style="font-size: 1.5em; color: #667eea;"><?php echo number_format($collection_rate, 2); ?>%</span>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>
