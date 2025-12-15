<?php
require_once '../config/db_connect.php';
include '../includes/header.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}

// Get filter parameters
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_mode = isset($_GET['mode']) ? $_GET['mode'] : '';

// Build query with filters
$where_clauses = array();
if ($filter_status) {
    $where_clauses[] = "p.status = '$filter_status'";
}
if ($filter_mode) {
    $where_clauses[] = "p.mode = '$filter_mode'";
}

$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Get all payments
$payments = fetch_all("
    SELECT 
        p.payment_id,
        s.name as student_name,
        s.email,
        c.course_name,
        f.semester,
        p.payment_date,
        p.amount_paid,
        p.mode,
        p.status,
        p.transaction_id,
        r.receipt_number
    FROM payment p
    INNER JOIN students s ON p.student_id = s.student_id
    INNER JOIN fees_structure f ON p.fee_id = f.fee_id
    INNER JOIN courses c ON f.course_id = c.course_id
    LEFT JOIN receipt r ON p.payment_id = r.payment_id
    $where_sql
    ORDER BY p.payment_date DESC, p.payment_id DESC
");
?>

<h2>💰 All Payments</h2>

<div style="margin-bottom: 20px;">
    <a href="make_payment.php" class="btn">➕ Make New Payment</a>
</div>

<div class="card">
    <h3>🔍 Filter Payments</h3>
    <form method="GET" action="">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" onchange="this.form.submit()">
                    <option value="">-- All Status --</option>
                    <option value="Completed" <?php echo ($filter_status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="Pending" <?php echo ($filter_status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Failed" <?php echo ($filter_status == 'Failed') ? 'selected' : ''; ?>>Failed</option>
                    <option value="Refunded" <?php echo ($filter_status == 'Refunded') ? 'selected' : ''; ?>>Refunded</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="mode">Payment Mode:</label>
                <select id="mode" name="mode" onchange="this.form.submit()">
                    <option value="">-- All Modes --</option>
                    <option value="Cash" <?php echo ($filter_mode == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                    <option value="Card" <?php echo ($filter_mode == 'Card') ? 'selected' : ''; ?>>Card</option>
                    <option value="UPI" <?php echo ($filter_mode == 'UPI') ? 'selected' : ''; ?>>UPI</option>
                    <option value="Net Banking" <?php echo ($filter_mode == 'Net Banking') ? 'selected' : ''; ?>>Net Banking</option>
                    <option value="Cheque" <?php echo ($filter_mode == 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                </select>
            </div>
        </div>
        <?php if ($filter_status || $filter_mode): ?>
            <a href="view_payments.php" class="btn btn-secondary" style="margin-top: 10px; display: inline-block; text-decoration: none; text-align: center;">Clear Filters</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>📋 Payment Records</h3>
    <?php if (count($payments) > 0): ?>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Semester</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Transaction ID</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo $payment['payment_id']; ?></td>
                    <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($payment['course_name']); ?></td>
                    <td>Sem <?php echo $payment['semester']; ?></td>
                    <td><?php echo date('d-M-Y', strtotime($payment['payment_date'])); ?></td>
                    <td>₹<?php echo number_format($payment['amount_paid'], 2); ?></td>
                    <td><?php echo $payment['mode']; ?></td>
                    <td>
                        <span style="padding: 5px 10px; border-radius: 3px; background: <?php 
                            echo $payment['status'] == 'Completed' ? '#d4edda' : 
                                ($payment['status'] == 'Pending' ? '#fff3cd' : '#f8d7da'); 
                        ?>; color: <?php 
                            echo $payment['status'] == 'Completed' ? '#155724' : 
                                ($payment['status'] == 'Pending' ? '#856404' : '#721c24'); 
                        ?>;">
                            <?php echo $payment['status']; ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                    <td>
                        <?php if ($payment['receipt_number']): ?>
                            <a href="generate_receipt.php?receipt=<?php echo $payment['receipt_number']; ?>" 
                               target="_blank" style="color: #667eea; text-decoration: none;">
                                📄 <?php echo $payment['receipt_number']; ?>
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p>No payments found. <a href="make_payment.php">Make your first payment</a></p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>📊 Payment Statistics</h3>
    <?php
    $stats = fetch_single("
        SELECT 
            COUNT(*) as total_payments,
            SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'Completed' THEN amount_paid ELSE 0 END) as total_amount,
            AVG(CASE WHEN status = 'Completed' THEN amount_paid ELSE NULL END) as avg_amount
        FROM payment
    ");
    
    $mode_stats = fetch_all("
        SELECT mode, COUNT(*) as count, SUM(amount_paid) as total
        FROM payment
        WHERE status = 'Completed'
        GROUP BY mode
        ORDER BY total DESC
    ");
    ?>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Total Payments:</strong><br>
            <span style="font-size: 1.5em; color: #667eea;"><?php echo $stats['total_payments']; ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Completed:</strong><br>
            <span style="font-size: 1.5em; color: #43e97b;"><?php echo $stats['completed']; ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Pending:</strong><br>
            <span style="font-size: 1.5em; color: #f5576c;"><?php echo $stats['pending']; ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Total Amount:</strong><br>
            <span style="font-size: 1.5em; color: #667eea;">₹<?php echo number_format($stats['total_amount'], 2); ?></span>
        </div>
        <div style="padding: 15px; background: #f8f9fa; border-radius: 5px;">
            <strong>Average Payment:</strong><br>
            <span style="font-size: 1.5em; color: #667eea;">₹<?php echo number_format($stats['avg_amount'], 2); ?></span>
        </div>
    </div>
    
    <h4>Payment Mode Distribution</h4>
    <table>
        <thead>
            <tr>
                <th>Payment Mode</th>
                <th>Transaction Count</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mode_stats as $mode): ?>
            <tr>
                <td><?php echo $mode['mode']; ?></td>
                <td><?php echo $mode['count']; ?></td>
                <td>₹<?php echo number_format($mode['total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include '../includes/footer.php';
?>
