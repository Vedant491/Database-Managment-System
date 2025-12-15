-- ============================================
-- Sample Data for College Fees Management System
-- ============================================

USE college_fees_db;

-- ============================================
-- INSERT DATA: courses
-- ============================================
INSERT INTO courses (course_name, duration_years, total_fees) VALUES
('Bachelor of Computer Science', 4, 200000.00),
('Bachelor of Business Administration', 3, 150000.00),
('Bachelor of Engineering - Mechanical', 4, 250000.00),
('Master of Computer Applications', 2, 120000.00),
('Bachelor of Arts', 3, 90000.00),
('Master of Business Administration', 2, 180000.00);

-- ============================================
-- INSERT DATA: students
-- ============================================
INSERT INTO students (name, email, phone, course_id, admission_year) VALUES
('Rahul Sharma', 'rahul.sharma@email.com', '9876543210', 1, 2023),
('Priya Patel', 'priya.patel@email.com', '9876543211', 1, 2023),
('Amit Kumar', 'amit.kumar@email.com', '9876543212', 2, 2022),
('Sneha Reddy', 'sneha.reddy@email.com', '9876543213', 3, 2023),
('Vikram Singh', 'vikram.singh@email.com', '9876543214', 4, 2024),
('Anjali Verma', 'anjali.verma@email.com', '9876543215', 2, 2023),
('Rohan Gupta', 'rohan.gupta@email.com', '9876543216', 5, 2022),
('Kavya Nair', 'kavya.nair@email.com', '9876543217', 1, 2024),
('Arjun Mehta', 'arjun.mehta@email.com', '9876543218', 6, 2024),
('Pooja Desai', 'pooja.desai@email.com', '9876543219', 3, 2023);

-- ============================================
-- INSERT DATA: fees_structure
-- ============================================
-- Computer Science (4 years = 8 semesters)
INSERT INTO fees_structure (course_id, semester, amount, description) VALUES
(1, 1, 25000.00, 'Semester 1 - Foundation Courses'),
(1, 2, 25000.00, 'Semester 2 - Core Programming'),
(1, 3, 25000.00, 'Semester 3 - Data Structures'),
(1, 4, 25000.00, 'Semester 4 - Database Systems'),
(1, 5, 25000.00, 'Semester 5 - Web Technologies'),
(1, 6, 25000.00, 'Semester 6 - Software Engineering'),
(1, 7, 25000.00, 'Semester 7 - Advanced Topics'),
(1, 8, 25000.00, 'Semester 8 - Project Work');

-- BBA (3 years = 6 semesters)
INSERT INTO fees_structure (course_id, semester, amount, description) VALUES
(2, 1, 25000.00, 'Semester 1 - Business Fundamentals'),
(2, 2, 25000.00, 'Semester 2 - Marketing Basics'),
(2, 3, 25000.00, 'Semester 3 - Financial Management'),
(2, 4, 25000.00, 'Semester 4 - Operations Management'),
(2, 5, 25000.00, 'Semester 5 - Strategic Management'),
(2, 6, 25000.00, 'Semester 6 - Internship & Project');

-- Mechanical Engineering (4 years = 8 semesters)
INSERT INTO fees_structure (course_id, semester, amount, description) VALUES
(3, 1, 31250.00, 'Semester 1 - Engineering Basics'),
(3, 2, 31250.00, 'Semester 2 - Mechanics'),
(3, 3, 31250.00, 'Semester 3 - Thermodynamics'),
(3, 4, 31250.00, 'Semester 4 - Fluid Mechanics'),
(3, 5, 31250.00, 'Semester 5 - Machine Design'),
(3, 6, 31250.00, 'Semester 6 - Manufacturing'),
(3, 7, 31250.00, 'Semester 7 - CAD/CAM'),
(3, 8, 31250.00, 'Semester 8 - Final Project');

-- MCA (2 years = 4 semesters)
INSERT INTO fees_structure (course_id, semester, amount, description) VALUES
(4, 1, 30000.00, 'Semester 1 - Advanced Programming'),
(4, 2, 30000.00, 'Semester 2 - System Design'),
(4, 3, 30000.00, 'Semester 3 - Cloud Computing'),
(4, 4, 30000.00, 'Semester 4 - Dissertation');

-- BA (3 years = 6 semesters)
INSERT INTO fees_structure (course_id, semester, amount, description) VALUES
(5, 1, 15000.00, 'Semester 1 - Foundation'),
(5, 2, 15000.00, 'Semester 2 - Core Subjects'),
(5, 3, 15000.00, 'Semester 3 - Specialization'),
(5, 4, 15000.00, 'Semester 4 - Electives'),
(5, 5, 15000.00, 'Semester 5 - Advanced Studies'),
(5, 6, 15000.00, 'Semester 6 - Project');

-- MBA (2 years = 4 semesters)
INSERT INTO fees_structure (course_id, semester, amount, description) VALUES
(6, 1, 45000.00, 'Semester 1 - Core Management'),
(6, 2, 45000.00, 'Semester 2 - Specialization'),
(6, 3, 45000.00, 'Semester 3 - Industry Project'),
(6, 4, 45000.00, 'Semester 4 - Internship');

-- ============================================
-- INSERT DATA: admin
-- Password: admin123 (hashed using MD5 for demo - use bcrypt in production)
-- ============================================
INSERT INTO admin (username, password, full_name, email) VALUES
('admin', MD5('admin123'), 'System Administrator', 'admin@college.edu'),
('accounts', MD5('accounts123'), 'Accounts Manager', 'accounts@college.edu'),
('registrar', MD5('registrar123'), 'Registrar Office', 'registrar@college.edu');

-- ============================================
-- INSERT DATA: payment
-- ============================================
INSERT INTO payment (student_id, fee_id, payment_date, amount_paid, mode, status, transaction_id, remarks) VALUES
-- Rahul Sharma (CS Student) - Paid Sem 1 & 2
(1, 1, '2023-07-15', 25000.00, 'UPI', 'Completed', 'TXN2023071501', 'First semester fee'),
(1, 2, '2024-01-10', 25000.00, 'Net Banking', 'Completed', 'TXN2024011001', 'Second semester fee'),

-- Priya Patel (CS Student) - Paid Sem 1
(2, 1, '2023-07-20', 25000.00, 'Card', 'Completed', 'TXN2023072001', 'Admission fee paid'),

-- Amit Kumar (BBA Student) - Paid Sem 1, 2, 3, 4
(3, 9, '2022-07-10', 25000.00, 'Cash', 'Completed', NULL, 'Cash payment'),
(3, 10, '2023-01-15', 25000.00, 'UPI', 'Completed', 'TXN2023011502', 'Semester 2'),
(3, 11, '2023-07-20', 25000.00, 'Net Banking', 'Completed', 'TXN2023072002', 'Semester 3'),
(3, 12, '2024-01-18', 25000.00, 'Card', 'Completed', 'TXN2024011802', 'Semester 4'),

-- Sneha Reddy (Mech Engg) - Paid Sem 1 & 2
(4, 15, '2023-08-01', 31250.00, 'Cheque', 'Completed', 'CHQ123456', 'Cheque payment'),
(4, 16, '2024-02-01', 31250.00, 'UPI', 'Completed', 'TXN2024020101', 'Second semester'),

-- Vikram Singh (MCA) - Paid Sem 1
(5, 23, '2024-08-05', 30000.00, 'Net Banking', 'Completed', 'TXN2024080501', 'MCA First semester'),

-- Anjali Verma (BBA) - Paid Sem 1 & 2
(6, 9, '2023-07-25', 25000.00, 'UPI', 'Completed', 'TXN2023072501', 'First payment'),
(6, 10, '2024-01-20', 25000.00, 'Card', 'Completed', 'TXN2024012001', 'Second semester'),

-- Rohan Gupta (BA) - Paid Sem 1, 2, 3, 4, 5
(7, 27, '2022-07-15', 15000.00, 'Cash', 'Completed', NULL, 'Cash payment'),
(7, 28, '2023-01-10', 15000.00, 'UPI', 'Completed', 'TXN2023011003', 'Semester 2'),
(7, 29, '2023-07-15', 15000.00, 'Net Banking', 'Completed', 'TXN2023071503', 'Semester 3'),
(7, 30, '2024-01-12', 15000.00, 'Card', 'Completed', 'TXN2024011203', 'Semester 4'),
(7, 31, '2024-07-10', 15000.00, 'UPI', 'Completed', 'TXN2024071001', 'Semester 5'),

-- Kavya Nair (CS) - Paid Sem 1
(8, 1, '2024-08-10', 25000.00, 'Net Banking', 'Completed', 'TXN2024081001', 'New admission'),

-- Arjun Mehta (MBA) - Paid Sem 1
(9, 33, '2024-08-15', 45000.00, 'Card', 'Completed', 'TXN2024081501', 'MBA First semester'),

-- Pooja Desai (Mech) - Paid Sem 1, Pending Sem 2
(10, 15, '2023-08-05', 31250.00, 'UPI', 'Completed', 'TXN2023080501', 'First semester'),
(10, 16, '2024-02-05', 31250.00, 'Cash', 'Pending', NULL, 'Payment pending verification');

-- ============================================
-- Note: Receipts are auto-generated by trigger
-- ============================================

-- Display summary
SELECT 'Data insertion completed successfully!' as Status;
SELECT COUNT(*) as Total_Courses FROM courses;
SELECT COUNT(*) as Total_Students FROM students;
SELECT COUNT(*) as Total_Fee_Structures FROM fees_structure;
SELECT COUNT(*) as Total_Payments FROM payment;
SELECT COUNT(*) as Total_Receipts FROM receipt;
