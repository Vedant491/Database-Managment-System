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

// Handle form submission for adding course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_course'])) {
    $course_name = sanitize_input($_POST['course_name']);
    $duration_years = sanitize_input($_POST['duration_years']);
    $total_fees = sanitize_input($_POST['total_fees']);
    
    if (empty($course_name) || empty($duration_years) || empty($total_fees)) {
        $error = "All fields are required.";
    } elseif ($duration_years < 1 || $duration_years > 6) {
        $error = "Duration must be between 1 and 6 years.";
    } elseif ($total_fees <= 0) {
        $error = "Total fees must be greater than 0.";
    } else {
        $query = "INSERT INTO courses (course_name, duration_years, total_fees) 
                  VALUES ('$course_name', $duration_years, $total_fees)";
        
        if (execute_query($query)) {
            $success = "Course added successfully!";
        } else {
            $error = "Failed to add course.";
        }
    }
}

// Get all courses
$courses = fetch_all("
    SELECT 
        c.*,
        COUNT(DISTINCT s.student_id) as enrolled_students,
        COALESCE(SUM(p.amount_paid), 0) as total_collected
    FROM courses c
    LEFT JOIN students s ON c.course_id = s.course_id
    LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
    GROUP BY c.course_id
    ORDER BY c.course_name
");
?>

<h2>📚 Manage Courses</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <h3>➕ Add New Course</h3>
    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="course_name">Course Name *</label>
                <input type="text" id="course_name" name="course_name" required>
            </div>
            
            <div class="form-group">
                <label for="duration_years">Duration (Years) *</label>
                <input type="number" id="duration_years" name="duration_years" required min="1" max="6">
            </div>
            
            <div class="form-group">
                <label for="total_fees">Total Fees (₹) *</label>
                <input type="number" id="total_fees" name="total_fees" required min="0" step="0.01">
            </div>
        </div>
        
        <button type="submit" name="add_course" class="btn">Add Course</button>
    </form>
</div>

<div class="card">
    <h3>📋 All Courses</h3>
    <?php if (count($courses) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Duration</th>
                <th>Total Fees</th>
                <th>Enrolled Students</th>
                <th>Total Collected</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
            <tr>
                <td><?php echo $course['course_id']; ?></td>
                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                <td><?php echo $course['duration_years']; ?> years</td>
                <td>₹<?php echo number_format($course['total_fees'], 2); ?></td>
                <td><?php echo $course['enrolled_students']; ?></td>
                <td>₹<?php echo number_format($course['total_collected'], 2); ?></td>
                <td><?php echo date('d-M-Y', strtotime($course['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p>No courses found.</p>
    <?php endif; ?>
</div>

<?php
include '../includes/footer.php';
?>
