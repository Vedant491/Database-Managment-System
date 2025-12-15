<?php
require_once '../config/db_connect.php';

// Get student ID from query parameter
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($student_id > 0) {
    // Get course ID for the student
    $student = fetch_single("SELECT course_id FROM students WHERE student_id = $student_id");
    
    if ($student) {
        $course_id = $student['course_id'];
        
        // Get fee structure for the course
        $fees = fetch_all("
            SELECT fee_id, semester, amount, description
            FROM fees_structure
            WHERE course_id = $course_id
            ORDER BY semester
        ");
        
        // Return as JSON
        header('Content-Type: application/json');
        echo json_encode($fees);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
