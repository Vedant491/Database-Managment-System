-- ============================================
-- SQL QUERIES - College Fees Management System
-- Demonstrates SELECT, UPDATE, DELETE, JOINs, Aggregations
-- ============================================

USE college_fees_db;

-- ============================================
-- SELECT QUERIES
-- ============================================

-- Query 1: Display all students with their course details
SELECT 
    s.student_id,
    s.name,
    s.email,
    s.phone,
    c.course_name,
    s.admission_year,
    c.total_fees
FROM students s
INNER JOIN courses c ON s.course_id = c.course_id
ORDER BY s.name;

-- Query 2: Show fee structure for a specific course (Computer Science)
SELECT 
    c.course_name,
    f.semester,
    f.amount,
    f.description
FROM fees_structure f
INNER JOIN courses c ON f.course_id = c.course_id
WHERE c.course_name = 'Bachelor of Computer Science'
ORDER BY f.semester;

-- Query 3: List all payments with student and course information
SELECT 
    p.payment_id,
    s.name AS student_name,
    c.course_name,
    f.semester,
    p.payment_date,
    p.amount_paid,
    p.mode,
    p.status,
    r.receipt_number
FROM payment p
INNER JOIN students s ON p.student_id = s.student_id
INNER JOIN fees_structure f ON p.fee_id = f.fee_id
INNER JOIN courses c ON f.course_id = c.course_id
LEFT JOIN receipt r ON p.payment_id = r.payment_id
ORDER BY p.payment_date DESC;

-- Query 4: Calculate total fees paid by each student
SELECT 
    s.student_id,
    s.name,
    c.course_name,
    c.total_fees,
    COALESCE(SUM(p.amount_paid), 0) AS total_paid,
    (c.total_fees - COALESCE(SUM(p.amount_paid), 0)) AS balance_due
FROM students s
INNER JOIN courses c ON s.course_id = c.course_id
LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
GROUP BY s.student_id, s.name, c.course_name, c.total_fees
ORDER BY balance_due DESC;

-- Query 5: Find students who haven't paid any fees yet
SELECT 
    s.student_id,
    s.name,
    s.email,
    c.course_name,
    c.total_fees
FROM students s
INNER JOIN courses c ON s.course_id = c.course_id
LEFT JOIN payment p ON s.student_id = p.student_id
WHERE p.payment_id IS NULL;

-- Query 6: Monthly payment report
SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') AS month,
    COUNT(*) AS total_transactions,
    SUM(amount_paid) AS total_amount,
    AVG(amount_paid) AS average_amount
FROM payment
WHERE status = 'Completed'
GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
ORDER BY month DESC;

-- Query 7: Payment mode analysis
SELECT 
    mode,
    COUNT(*) AS transaction_count,
    SUM(amount_paid) AS total_amount,
    ROUND(AVG(amount_paid), 2) AS avg_amount
FROM payment
WHERE status = 'Completed'
GROUP BY mode
ORDER BY total_amount DESC;

-- Query 8: Students with pending payments
SELECT 
    s.name,
    s.email,
    c.course_name,
    f.semester,
    p.amount_paid,
    p.payment_date,
    p.status
FROM payment p
INNER JOIN students s ON p.student_id = s.student_id
INNER JOIN fees_structure f ON p.fee_id = f.fee_id
INNER JOIN courses c ON s.course_id = c.course_id
WHERE p.status = 'Pending';

-- Query 9: Course-wise revenue report
SELECT 
    c.course_name,
    COUNT(DISTINCT s.student_id) AS enrolled_students,
    c.total_fees AS expected_per_student,
    COALESCE(SUM(p.amount_paid), 0) AS total_collected,
    (COUNT(DISTINCT s.student_id) * c.total_fees) AS total_expected,
    ((COUNT(DISTINCT s.student_id) * c.total_fees) - COALESCE(SUM(p.amount_paid), 0)) AS pending_amount
FROM courses c
LEFT JOIN students s ON c.course_id = s.course_id
LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
GROUP BY c.course_id, c.course_name, c.total_fees
ORDER BY total_collected DESC;

-- Query 10: Recent payments with receipt details (Last 10)
SELECT 
    r.receipt_number,
    s.name AS student_name,
    c.course_name,
    p.amount_paid,
    p.payment_date,
    p.mode,
    r.generated_date
FROM receipt r
INNER JOIN payment p ON r.payment_id = p.payment_id
INNER JOIN students s ON p.student_id = s.student_id
INNER JOIN fees_structure f ON p.fee_id = f.fee_id
INNER JOIN courses c ON f.course_id = c.course_id
ORDER BY r.generated_date DESC
LIMIT 10;

-- Query 11: Students who completed full payment
SELECT 
    s.student_id,
    s.name,
    c.course_name,
    c.total_fees,
    SUM(p.amount_paid) AS total_paid
FROM students s
INNER JOIN courses c ON s.course_id = c.course_id
INNER JOIN payment p ON s.student_id = p.student_id
WHERE p.status = 'Completed'
GROUP BY s.student_id, s.name, c.course_name, c.total_fees
HAVING SUM(p.amount_paid) >= c.total_fees;

-- Query 12: Semester-wise payment status for a student
SELECT 
    f.semester,
    f.amount AS required_amount,
    COALESCE(p.amount_paid, 0) AS paid_amount,
    COALESCE(p.payment_date, 'Not Paid') AS payment_date,
    COALESCE(p.status, 'Unpaid') AS status
FROM fees_structure f
LEFT JOIN payment p ON f.fee_id = p.fee_id AND p.student_id = 1
WHERE f.course_id = (SELECT course_id FROM students WHERE student_id = 1)
ORDER BY f.semester;

-- ============================================
-- UPDATE QUERIES
-- ============================================

-- Update 1: Change student email
UPDATE students 
SET email = 'rahul.sharma.new@email.com'
WHERE student_id = 1;

-- Update 2: Update payment status
UPDATE payment 
SET status = 'Completed', remarks = 'Payment verified and approved'
WHERE payment_id = 20 AND status = 'Pending';

-- Update 3: Modify course fees
UPDATE courses 
SET total_fees = 210000.00
WHERE course_id = 1;

-- Update 4: Update admin last login
UPDATE admin 
SET last_login = NOW()
WHERE username = 'admin';

-- Update 5: Change payment mode
UPDATE payment 
SET mode = 'Net Banking', transaction_id = 'TXN2024120401'
WHERE payment_id = 5;

-- Update 6: Bulk update - Add 5% to all course fees
UPDATE courses 
SET total_fees = total_fees * 1.05;

-- Update 7: Update student phone number
UPDATE students 
SET phone = '9999999999'
WHERE student_id = 3;

-- ============================================
-- DELETE QUERIES
-- ============================================

-- Delete 1: Remove a specific payment (will cascade to receipt)
DELETE FROM payment 
WHERE payment_id = 100 AND status = 'Failed';

-- Delete 2: Delete student (will cascade to payments and receipts)
-- First, let's add a test student to delete
INSERT INTO students (name, email, phone, course_id, admission_year)
VALUES ('Test Student', 'test@email.com', '1111111111', 1, 2024);

DELETE FROM students 
WHERE email = 'test@email.com';

-- Delete 3: Remove old pending payments (older than 1 year)
DELETE FROM payment 
WHERE status = 'Pending' 
AND payment_date < DATE_SUB(CURDATE(), INTERVAL 1 YEAR);

-- Delete 4: Remove a course (only if no students enrolled - RESTRICT constraint)
-- This will fail if students are enrolled
DELETE FROM courses 
WHERE course_id = 999;

-- ============================================
-- ADVANCED QUERIES
-- ============================================

-- Query 13: Find defaulters (students with balance > 50% of total fees)
SELECT 
    s.student_id,
    s.name,
    s.email,
    s.phone,
    c.course_name,
    c.total_fees,
    COALESCE(SUM(p.amount_paid), 0) AS paid,
    (c.total_fees - COALESCE(SUM(p.amount_paid), 0)) AS balance,
    ROUND(((c.total_fees - COALESCE(SUM(p.amount_paid), 0)) / c.total_fees * 100), 2) AS balance_percentage
FROM students s
INNER JOIN courses c ON s.course_id = c.course_id
LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
GROUP BY s.student_id, s.name, s.email, s.phone, c.course_name, c.total_fees
HAVING balance_percentage > 50
ORDER BY balance_percentage DESC;

-- Query 14: Year-wise admission and revenue analysis
SELECT 
    s.admission_year,
    COUNT(s.student_id) AS total_students,
    SUM(c.total_fees) AS expected_revenue,
    COALESCE(SUM(p.amount_paid), 0) AS collected_revenue,
    ROUND((COALESCE(SUM(p.amount_paid), 0) / SUM(c.total_fees) * 100), 2) AS collection_percentage
FROM students s
INNER JOIN courses c ON s.course_id = c.course_id
LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
GROUP BY s.admission_year
ORDER BY s.admission_year DESC;

-- Query 15: Top 5 courses by enrollment
SELECT 
    c.course_name,
    COUNT(s.student_id) AS enrolled_students,
    c.total_fees,
    (COUNT(s.student_id) * c.total_fees) AS potential_revenue
FROM courses c
LEFT JOIN students s ON c.course_id = s.course_id
GROUP BY c.course_id, c.course_name, c.total_fees
ORDER BY enrolled_students DESC
LIMIT 5;

-- ============================================
-- SUBQUERIES
-- ============================================

-- Query 16: Students who paid more than average payment
SELECT 
    s.name,
    p.amount_paid,
    p.payment_date
FROM students s
INNER JOIN payment p ON s.student_id = p.student_id
WHERE p.amount_paid > (SELECT AVG(amount_paid) FROM payment)
ORDER BY p.amount_paid DESC;

-- Query 17: Courses with highest collection rate
SELECT 
    course_name,
    total_fees,
    (SELECT COUNT(*) FROM students WHERE course_id = c.course_id) AS students,
    (SELECT COALESCE(SUM(amount_paid), 0) 
     FROM payment p 
     INNER JOIN students s ON p.student_id = s.student_id 
     WHERE s.course_id = c.course_id AND p.status = 'Completed') AS collected
FROM courses c
ORDER BY collected DESC;

-- ============================================
-- AGGREGATE FUNCTIONS
-- ============================================

-- Query 18: Overall statistics
SELECT 
    (SELECT COUNT(*) FROM students) AS total_students,
    (SELECT COUNT(*) FROM courses) AS total_courses,
    (SELECT COUNT(*) FROM payment WHERE status = 'Completed') AS completed_payments,
    (SELECT SUM(amount_paid) FROM payment WHERE status = 'Completed') AS total_revenue,
    (SELECT AVG(amount_paid) FROM payment WHERE status = 'Completed') AS avg_payment,
    (SELECT MAX(amount_paid) FROM payment) AS max_payment,
    (SELECT MIN(amount_paid) FROM payment) AS min_payment;

-- Display completion message
SELECT '=== All queries executed successfully ===' AS Status;
