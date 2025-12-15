<?php
require_once 'config/db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin/login.php");
    exit();
}

include 'includes/header.php';
?>

<h2>📊 Dashboard</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 30px 0;">
    <?php
    // Get statistics
    $total_students = fetch_single("SELECT COUNT(*) as count FROM students")['count'];
    $total_courses = fetch_single("SELECT COUNT(*) as count FROM courses")['count'];
    $total_payments = fetch_single("SELECT COUNT(*) as count FROM payment WHERE status='Completed'")['count'];
    $total_revenue = fetch_single("SELECT COALESCE(SUM(amount_paid), 0) as total FROM payment WHERE status='Completed'")['total'];
    $pending_payments = fetch_single("SELECT COUNT(*) as count FROM payment WHERE status='Pending'")['count'];
    ?>
    
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h3>👨‍🎓 Total Students</h3>
        <p style="font-size: 3em; font-weight: bold;"><?php echo $total_students; ?></p>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <h3>📚 Total Courses</h3>
        <p style="font-size: 3em; font-weight: bold;"><?php echo $total_courses; ?></p>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <h3>💰 Total Payments</h3>
        <p style="font-size: 3em; font-weight: bold;"><?php echo $total_payments; ?></p>
    </div>

    <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <h3>💵 Total Revenue</h3>
        <p style="font-size: 2em; font-weight: bold;">₹<?php echo number_format($total_revenue, 2); ?></p>
    </div>
</div>

<?php if ($pending_payments > 0): ?>
<div class="alert alert-error">
    ⚠️ You have <?php echo $pending_payments; ?> pending payment(s) that need verification.
</div>
<?php endif; ?>

<div class="card">
    <h3>📈 Recent Payments</h3>

    <?php
    $recent_payments = fetch_all("
        SELECT 
            p.payment_id,
            s.name as student_name,
            c.course_name,
            p.payment_date,
            p.amount_paid,
            p.mode,
            p.status
        FROM payment p
        INNER JOIN students s ON p.student_id = s.student_id
        INNER JOIN fees_structure f ON p.fee_id = f.fee_id
        INNER JOIN courses c ON f.course_id = c.course_id
        ORDER BY p.payment_date DESC
        LIMIT 10
    ");

    if (count($recent_payments) > 0):
    ?>

    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Student Name</th>
                <th>Course</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Mode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_payments as $payment): ?>
            <tr>
                <td><?php echo $payment['payment_id']; ?></td>
                <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                <td><?php echo htmlspecialchars($payment['course_name']); ?></td>
                <td><?php echo date('d-M-Y', strtotime($payment['payment_date'])); ?></td>
                <td>₹<?php echo number_format($payment['amount_paid'], 2); ?></td>
                <td><?php echo $payment['mode']; ?></td>
                <td>
                    <span style="padding: 5px 10px; border-radius: 3px; background: <?php echo $payment['status'] == 'Completed' ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $payment['status'] == 'Completed' ? '#155724' : '#856404'; ?>;">
                        <?php echo $payment['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>
        <p>No payments recorded yet.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>📊 Course-wise Revenue</h3>

    <?php
    $course_revenue = fetch_all("
        SELECT 
            c.course_name,
            COUNT(DISTINCT s.student_id) AS enrolled_students,
            COALESCE(SUM(p.amount_paid), 0) AS total_collected
        FROM courses c
        LEFT JOIN students s ON c.course_id = s.course_id
        LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
        GROUP BY c.course_id, c.course_name
        ORDER BY total_collected DESC
    ");
    ?>

    <table>
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Enrolled Students</th>
                <th>Total Collected</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($course_revenue as $course): ?>
            <tr>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo $course['enrolled_students']; ?></td>
                <td>₹<?php echo number_format($course['total_collected'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
