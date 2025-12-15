<?php
require_once '../config/db_connect.php';
include '../includes/header.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin/login.php");
    exit();
}

$error = '';
$success = '';

// Handle form submission for adding fee structure
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_fee'])) {
    $course_id = sanitize_input($_POST['course_id']);
    $semester = sanitize_input($_POST['semester']);
    $amount = sanitize_input($_POST['amount']);
    $description = sanitize_input($_POST['description']);
    
    if (empty($course_id) || empty($semester) || empty($amount)) {
        $error = "Course, semester, and amount are required.";
    } elseif ($semester < 1 || $semester > 12) {
        $error = "Semester must be between 1 and 12.";
    } elseif ($amount <= 0) {
        $error = "Amount must be greater than 0.";
    } else {
        $query = "INSERT INTO fees_structure (course_id, semester, amount, description) 
                  VALUES ($course_id, $semester, $amount, '$description')";
        
        if (execute_query($query)) {
            $success = "Fee structure added successfully!";
        } else {
            $error = "Failed to add fee structure. This semester may already exist for this course.";
        }
    }
}

// Get all courses for dropdown
$courses = fetch_all("SELECT * FROM courses ORDER BY course_name");

// Get selected course ID
$selected_course = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Get fee structure
if ($selected_course > 0) {
    $fees = fetch_all("
        SELECT f.*, c.course_name
        FROM fees_structure f
        INNER JOIN courses c ON f.course_id = c.course_id
        WHERE f.course_id = $selected_course
        ORDER BY f.semester
    ");
} else {
    $fees = fetch_all("
        SELECT f.*, c.course_name
        FROM fees_structure f
        INNER JOIN courses c ON f.course_id = c.course_id
        ORDER BY c.course_name, f.semester
    ");
}
?>

<h2>💰 Fee Structure Management</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <h3>➕ Add Fee Structure</h3>
    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="course_id">Select Course *</label>
                <select id="course_id" name="course_id" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['course_id']; ?>">
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester">Semester *</label>
                <input type="number" id="semester" name="semester" required min="1" max="12">
            </div>
            
            <div class="form-group">
                <label for="amount">Amount (₹) *</label>
                <input type="number" id="amount" name="amount" required min="0" step="0.01">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description">
            </div>
        </div>
        
        <button type="submit" name="add_fee" class="btn">Add Fee Structure</button>
    </form>
</div>

<div class="card">
    <h3>📋 Fee Structure</h3>
    
    <form method="GET" action="" style="margin-bottom: 20px;">
        <div class="form-group" style="max-width: 400px;">
            <label for="filter_course">Filter by Course:</label>
            <select id="filter_course" name="course_id" onchange="this.form.submit()">
                <option value="0">-- All Courses --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>" 
                            <?php echo ($selected_course == $course['course_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
    
    <?php if (count($fees) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Fee ID</th>
                <th>Course Name</th>
                <th>Semester</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fees as $fee): ?>
            <tr>
                <td><?php echo $fee['fee_id']; ?></td>
                <td><?php echo htmlspecialchars($fee['course_name']); ?></td>
                <td>Semester <?php echo $fee['semester']; ?></td>
                <td>₹<?php echo number_format($fee['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars($fee['description']); ?></td>
                <td><?php echo date('d-M-Y', strtotime($fee['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No fee structure found.</p>
    <?php endif; ?>
</div>

<?php
include '../includes/footer.php';
?>
