-- ============================================
-- College Fees Management System - Database Schema
-- ============================================

-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS college_fees_db;
CREATE DATABASE college_fees_db;
USE college_fees_db;

-- ============================================
-- TABLE 1: courses
-- Stores information about courses offered
-- ============================================
CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL UNIQUE,
    duration_years INT NOT NULL CHECK (duration_years > 0 AND duration_years <= 6),
    total_fees DECIMAL(10, 2) NOT NULL CHECK (total_fees > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLE 2: students
-- Stores student information with foreign key to courses
-- ============================================
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    course_id INT NOT NULL,
    admission_year YEAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CHECK (phone REGEXP '^[0-9]{10,15}$')
) ENGINE=InnoDB;

-- ============================================
-- TABLE 3: fees_structure
-- Defines semester-wise fee structure for each course
-- ============================================
CREATE TABLE fees_structure (
    fee_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    semester INT NOT NULL CHECK (semester > 0 AND semester <= 12),
    amount DECIMAL(10, 2) NOT NULL CHECK (amount > 0),
    description VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    UNIQUE KEY unique_course_semester (course_id, semester)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 4: admin
-- Stores admin credentials for system access
-- ============================================
CREATE TABLE admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
) ENGINE=InnoDB;

-- ============================================
-- TABLE 5: payment
-- Records all fee payments made by students
-- ============================================
CREATE TABLE payment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    fee_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL CHECK (amount_paid > 0),
    mode ENUM('Cash', 'Card', 'UPI', 'Net Banking', 'Cheque') NOT NULL,
    status ENUM('Pending', 'Completed', 'Failed', 'Refunded') DEFAULT 'Completed',
    transaction_id VARCHAR(100),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    FOREIGN KEY (fee_id) REFERENCES fees_structure(fee_id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLE 6: receipt
-- Generates receipt for each completed payment
-- ============================================
CREATE TABLE receipt (
    receipt_id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL UNIQUE,
    generated_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    FOREIGN KEY (payment_id) REFERENCES payment(payment_id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- INDEXES for Performance Optimization
-- ============================================
CREATE INDEX idx_student_course ON students(course_id);
CREATE INDEX idx_student_email ON students(email);
CREATE INDEX idx_payment_student ON payment(student_id);
CREATE INDEX idx_payment_date ON payment(payment_date);
CREATE INDEX idx_payment_status ON payment(status);
CREATE INDEX idx_fees_course ON fees_structure(course_id);

-- ============================================
-- VIEWS for Common Queries
-- ============================================

-- View: Student Payment Summary
CREATE VIEW student_payment_summary AS
SELECT 
    s.student_id,
    s.name,
    s.email,
    c.course_name,
    COUNT(p.payment_id) as total_payments,
    SUM(p.amount_paid) as total_paid,
    c.total_fees,
    (c.total_fees - COALESCE(SUM(p.amount_paid), 0)) as balance_due
FROM students s
JOIN courses c ON s.course_id = c.course_id
LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
GROUP BY s.student_id, s.name, s.email, c.course_name, c.total_fees;

-- View: Payment Details with Receipt
CREATE VIEW payment_receipt_details AS
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
    r.receipt_number,
    r.generated_date
FROM payment p
JOIN students s ON p.student_id = s.student_id
JOIN fees_structure f ON p.fee_id = f.fee_id
JOIN courses c ON f.course_id = c.course_id
LEFT JOIN receipt r ON p.payment_id = r.payment_id;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER //

-- Procedure: Add new student with validation
CREATE PROCEDURE add_student(
    IN p_name VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_phone VARCHAR(15),
    IN p_course_id INT,
    IN p_admission_year YEAR
)
BEGIN
    DECLARE course_exists INT;
    
    -- Check if course exists
    SELECT COUNT(*) INTO course_exists FROM courses WHERE course_id = p_course_id;
    
    IF course_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Course does not exist';
    END IF;
    
    INSERT INTO students (name, email, phone, course_id, admission_year)
    VALUES (p_name, p_email, p_phone, p_course_id, p_admission_year);
END //

-- Procedure: Record payment and generate receipt
CREATE PROCEDURE record_payment(
    IN p_student_id INT,
    IN p_fee_id INT,
    IN p_payment_date DATE,
    IN p_amount_paid DECIMAL(10, 2),
    IN p_mode VARCHAR(20),
    IN p_transaction_id VARCHAR(100)
)
BEGIN
    DECLARE new_payment_id INT;
    DECLARE receipt_num VARCHAR(50);
    
    -- Insert payment
    INSERT INTO payment (student_id, fee_id, payment_date, amount_paid, mode, status, transaction_id)
    VALUES (p_student_id, p_fee_id, p_payment_date, p_amount_paid, p_mode, 'Completed', p_transaction_id);
    
    SET new_payment_id = LAST_INSERT_ID();
    
    -- Generate receipt number
    SET receipt_num = CONCAT('RCP', YEAR(CURDATE()), LPAD(new_payment_id, 6, '0'));
    
    -- Insert receipt
    INSERT INTO receipt (payment_id, receipt_number)
    VALUES (new_payment_id, receipt_num);
    
    SELECT new_payment_id as payment_id, receipt_num as receipt_number;
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger: Auto-generate receipt after payment
CREATE TRIGGER after_payment_insert
AFTER INSERT ON payment
FOR EACH ROW
BEGIN
    DECLARE receipt_num VARCHAR(50);
    
    IF NEW.status = 'Completed' THEN
        SET receipt_num = CONCAT('RCP', YEAR(CURDATE()), LPAD(NEW.payment_id, 6, '0'));
        
        INSERT INTO receipt (payment_id, receipt_number)
        VALUES (NEW.payment_id, receipt_num)
        ON DUPLICATE KEY UPDATE receipt_number = receipt_num;
    END IF;
END //

DELIMITER ;

-- ============================================
-- Display Schema Information
-- ============================================
SHOW TABLES;
