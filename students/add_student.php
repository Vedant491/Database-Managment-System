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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $course_id = sanitize_input($_POST['course_id']);
    $admission_year = sanitize_input($_POST['admission_year']);
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($course_id) || empty($admission_year)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        $error = "Phone number must be 10-15 digits.";
    } else {
        // Check if email already exists
        $check_email = fetch_single("SELECT * FROM students WHERE email = '$email'");
        if ($check_email) {
            $error = "Email already exists.";
        } else {
            // Insert student
            $query = "INSERT INTO students (name, email, phone, course_id, admission_year) 
                      VALUES ('$name', '$email', '$phone', $course_id, $admission_year)";
            
            if (execute_query($query)) {
                $success = "Student added successfully! Student ID: " . get_last_id();
                // Clear form
                $_POST = array();
            } else {
                $error = "Failed to add student.";
            }
        }
    }
}

// Get all courses for dropdown
$courses = fetch_all("SELECT * FROM courses ORDER BY course_name");
?>

<h2>➕ Add New Student</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Student Name *</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="phone">Phone Number * (10-15 digits)</label>
            <input type="text" id="phone" name="phone" required pattern="[0-9]{10,15}"
                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="course_id">Select Course *</label>
            <select id="course_id" name="course_id" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>"
                            <?php echo (isset($_POST['course_id']) && $_POST['course_id'] == $course['course_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_name']); ?> 
                        (₹<?php echo number_format($course['total_fees'], 2); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="admission_year">Admission Year *</label>
            <input type="number" id="admission_year" name="admission_year" required 
                   min="2020" max="<?php echo date('Y'); ?>"
                   value="<?php echo isset($_POST['admission_year']) ? $_POST['admission_year'] : date('Y'); ?>">
        </div>
        
        <button type="submit" class="btn">Add Student</button>
        <a href="view_students.php" class="btn btn-secondary" style="margin-left: 10px; display: inline-block; text-decoration: none; text-align: center;">View All Students</a>
    </form>
</div>

<?php
include '../includes/footer.php';
?>
