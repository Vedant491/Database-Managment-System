<?php
require_once '../config/db_connect.php';

// Get receipt number from query parameter
$receipt_number = isset($_GET['receipt']) ? sanitize_input($_GET['receipt']) : '';

if (empty($receipt_number)) {
    die("Receipt number is required.");
}

// Get receipt details
$receipt_data = fetch_single("
    SELECT 
        r.receipt_id,
        r.receipt_number,
        r.generated_date,
        p.payment_id,
        p.payment_date,
        p.amount_paid,
        p.mode,
        p.transaction_id,
        p.remarks,
        s.student_id,
        s.name as student_name,
        s.email as student_email,
        s.phone as student_phone,
        c.course_name,
        c.duration_years,
        f.semester,
        f.amount as semester_fee,
        f.description as fee_description
    FROM receipt r
    INNER JOIN payment p ON r.payment_id = p.payment_id
    INNER JOIN students s ON p.student_id = s.student_id
    INNER JOIN fees_structure f ON p.fee_id = f.fee_id
    INNER JOIN courses c ON f.course_id = c.course_id
    WHERE r.receipt_number = '$receipt_number'
");

if (!$receipt_data) {
    die("Receipt not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - <?php echo $receipt_data['receipt_number']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border: 2px solid #333;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .receipt-header h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 5px;
        }
        
        .receipt-header p {
            color: #666;
            font-size: 1.1em;
        }
        
        .receipt-number {
            text-align: center;
            background: #667eea;
            color: white;
            padding: 10px;
            margin-bottom: 30px;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-section h3 {
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: bold;
            color: #333;
        }
        
        .info-value {
            color: #666;
        }
        
        .amount-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 30px 0;
        }
        
        .amount-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 1.1em;
        }
        
        .total-amount {
            border-top: 2px solid #667eea;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #333;
            text-align: center;
            color: #666;
        }
        
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin-left: auto;
            margin-top: 50px;
            padding-top: 10px;
            text-align: center;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .print-button {
                display: none;
            }
            
            .receipt-container {
                border: none;
                box-shadow: none;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()" class="btn">🖨️ Print Receipt</button>
        <a href="view_payments.php" class="btn" style="background: #6c757d;">← Back to Payments</a>
    </div>
    
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>🎓 COLLEGE FEES RECEIPT</h1>
            <p>College Fees Management System</p>
            <p style="font-size: 0.9em; margin-top: 5px;">
                Address: 123 College Street, Education City<br>
                Phone: +91-1234567890 | Email: fees@college.edu
            </p>
        </div>
        
        <div class="receipt-number">
            RECEIPT NO: <?php echo $receipt_data['receipt_number']; ?>
        </div>
        
        <div class="info-section">
            <h3>📋 Student Information</h3>
            <div class="info-row">
                <div class="info-label">Student ID:</div>
                <div class="info-value"><?php echo $receipt_data['student_id']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Student Name:</div>
                <div class="info-value"><?php echo htmlspecialchars($receipt_data['student_name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo htmlspecialchars($receipt_data['student_email']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value"><?php echo htmlspecialchars($receipt_data['student_phone']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Course:</div>
                <div class="info-value"><?php echo htmlspecialchars($receipt_data['course_name']); ?></div>
            </div>
        </div>
        
        <div class="info-section">
            <h3>💰 Payment Details</h3>
            <div class="info-row">
                <div class="info-label">Payment ID:</div>
                <div class="info-value"><?php echo $receipt_data['payment_id']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Date:</div>
                <div class="info-value"><?php echo date('d-M-Y', strtotime($receipt_data['payment_date'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Receipt Date:</div>
                <div class="info-value"><?php echo date('d-M-Y H:i:s', strtotime($receipt_data['generated_date'])); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Semester:</div>
                <div class="info-value">Semester <?php echo $receipt_data['semester']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Fee Description:</div>
                <div class="info-value"><?php echo htmlspecialchars($receipt_data['fee_description']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Payment Mode:</div>
                <div class="info-value"><?php echo $receipt_data['mode']; ?></div>
            </div>
            <?php if ($receipt_data['transaction_id']): ?>
            <div class="info-row">
                <div class="info-label">Transaction ID:</div>
                <div class="info-value"><?php echo htmlspecialchars($receipt_data['transaction_id']); ?></div>
            </div>
            <?php endif; ?>
            <?php if ($receipt_data['remarks']): ?>
            <div class="info-row">
                <div class="info-label">Remarks:</div>
                <div class="info-value"><?php echo htmlspecialchars($receipt_data['remarks']); ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="amount-section">
            <div class="amount-row">
                <span>Semester Fee Amount:</span>
                <span>₹<?php echo number_format($receipt_data['semester_fee'], 2); ?></span>
            </div>
            <div class="amount-row total-amount">
                <span>Amount Paid:</span>
                <span>₹<?php echo number_format($receipt_data['amount_paid'], 2); ?></span>
            </div>
            <div style="margin-top: 15px; padding: 10px; background: white; border-radius: 3px;">
                <strong>Amount in Words:</strong><br>
                <?php
                // Simple number to words conversion (basic implementation)
                $amount = $receipt_data['amount_paid'];
                echo "Rupees " . ucwords(strtolower(number_format($amount, 2))) . " Only";
                ?>
            </div>
        </div>
        
        <div class="signature">
            <div class="signature-line">
                Authorized Signature
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Thank you for your payment!</strong></p>
            <p style="font-size: 0.9em; margin-top: 10px;">
                This is a computer-generated receipt and does not require a physical signature.<br>
                For any queries, please contact the accounts department.
            </p>
            <p style="font-size: 0.8em; margin-top: 10px; color: #999;">
                Generated on: <?php echo date('d-M-Y H:i:s'); ?>
            </p>
        </div>
    </div>
</body>
</html>
