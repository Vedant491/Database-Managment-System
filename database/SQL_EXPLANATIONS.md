# SQL Commands Explanation - College Fees Management System

## Table of Contents
1. [DDL Commands (Data Definition Language)](#ddl-commands)
2. [DML Commands (Data Manipulation Language)](#dml-commands)
3. [DQL Commands (Data Query Language)](#dql-commands)
4. [Constraints](#constraints)
5. [Joins](#joins)
6. [Aggregate Functions](#aggregate-functions)
7. [Subqueries](#subqueries)
8. [Views](#views)
9. [Stored Procedures](#stored-procedures)
10. [Triggers](#triggers)

---

## DDL Commands (Data Definition Language)

### CREATE DATABASE
Creates a new database.

```sql
CREATE DATABASE college_fees_db;
```

**Explanation:**
- Creates a database named `college_fees_db`
- This is the container for all tables

### USE DATABASE
Selects a database to work with.

```sql
USE college_fees_db;
```

**Explanation:**
- All subsequent commands will execute in this database

### CREATE TABLE
Creates a new table with specified columns and constraints.

```sql
CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL UNIQUE,
    duration_years INT NOT NULL CHECK (duration_years > 0 AND duration_years <= 6),
    total_fees DECIMAL(10, 2) NOT NULL CHECK (total_fees > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

**Explanation:**
- `course_id`: Integer, primary key, auto-increments
- `course_name`: Variable character (max 100), must be unique and not null
- `duration_years`: Integer with CHECK constraint (1-6 years)
- `total_fees`: Decimal with 10 digits total, 2 after decimal point
- `created_at`: Timestamp, automatically set to current time
- `ENGINE=InnoDB`: Uses InnoDB engine (supports foreign keys)

### ALTER TABLE
Modifies existing table structure.

```sql
-- Add new column
ALTER TABLE students ADD COLUMN address VARCHAR(200);

-- Modify column
ALTER TABLE students MODIFY COLUMN phone VARCHAR(20);

-- Drop column
ALTER TABLE students DROP COLUMN address;
```

### DROP TABLE
Deletes a table and all its data.

```sql
DROP TABLE IF EXISTS table_name;
```

**Warning:** This permanently deletes the table!

---

## DML Commands (Data Manipulation Language)

### INSERT
Adds new records to a table.

```sql
-- Insert single record
INSERT INTO courses (course_name, duration_years, total_fees) 
VALUES ('Bachelor of Computer Science', 4, 200000.00);

-- Insert multiple records
INSERT INTO courses (course_name, duration_years, total_fees) VALUES
('Bachelor of Business Administration', 3, 150000.00),
('Bachelor of Engineering', 4, 250000.00);
```

**Explanation:**
- Specifies table name and columns
- VALUES clause provides data for each column
- AUTO_INCREMENT columns (like IDs) are omitted

### UPDATE
Modifies existing records.

```sql
-- Update single record
UPDATE students 
SET email = 'newemail@example.com'
WHERE student_id = 1;

-- Update multiple records
UPDATE courses 
SET total_fees = total_fees * 1.05
WHERE duration_years = 4;

-- Update with calculation
UPDATE payment 
SET status = 'Completed', remarks = 'Payment verified'
WHERE payment_id = 10;
```

**Explanation:**
- SET clause specifies new values
- WHERE clause filters which records to update
- **Important:** Without WHERE, ALL records are updated!

### DELETE
Removes records from a table.

```sql
-- Delete specific record
DELETE FROM payment 
WHERE payment_id = 100;

-- Delete with condition
DELETE FROM payment 
WHERE status = 'Failed' AND payment_date < '2023-01-01';
```

**Explanation:**
- WHERE clause specifies which records to delete
- **Important:** Without WHERE, ALL records are deleted!
- Foreign key constraints may prevent deletion

---

## DQL Commands (Data Query Language)

### SELECT - Basic Queries

```sql
-- Select all columns
SELECT * FROM students;

-- Select specific columns
SELECT name, email, phone FROM students;

-- Select with alias
SELECT name AS student_name, email AS contact_email FROM students;

-- Select distinct values
SELECT DISTINCT course_id FROM students;
```

### WHERE Clause - Filtering

```sql
-- Single condition
SELECT * FROM students WHERE course_id = 1;

-- Multiple conditions (AND)
SELECT * FROM students 
WHERE course_id = 1 AND admission_year = 2023;

-- Multiple conditions (OR)
SELECT * FROM payment 
WHERE mode = 'Cash' OR mode = 'UPI';

-- IN operator
SELECT * FROM students 
WHERE course_id IN (1, 2, 3);

-- BETWEEN operator
SELECT * FROM payment 
WHERE payment_date BETWEEN '2023-01-01' AND '2023-12-31';

-- LIKE operator (pattern matching)
SELECT * FROM students 
WHERE name LIKE 'A%';  -- Names starting with A

SELECT * FROM students 
WHERE email LIKE '%@gmail.com';  -- Gmail addresses
```

### ORDER BY - Sorting

```sql
-- Ascending order (default)
SELECT * FROM students ORDER BY name;

-- Descending order
SELECT * FROM students ORDER BY admission_year DESC;

-- Multiple columns
SELECT * FROM students 
ORDER BY course_id ASC, name ASC;
```

### LIMIT - Restricting Results

```sql
-- First 10 records
SELECT * FROM students LIMIT 10;

-- Skip 5, then get 10 (pagination)
SELECT * FROM students LIMIT 5, 10;
```

---

## Constraints

### PRIMARY KEY
Uniquely identifies each record.

```sql
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    -- other columns
);
```

**Properties:**
- Must be unique
- Cannot be NULL
- Only one per table
- Auto-indexed

### FOREIGN KEY
Links two tables together.

```sql
CREATE TABLE students (
    student_id INT PRIMARY KEY,
    course_id INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(course_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);
```

**Explanation:**
- `course_id` in students references `course_id` in courses
- `ON DELETE RESTRICT`: Cannot delete course if students exist
- `ON UPDATE CASCADE`: Updates propagate to students table

### UNIQUE
Ensures all values in column are different.

```sql
CREATE TABLE students (
    email VARCHAR(100) NOT NULL UNIQUE
);
```

### NOT NULL
Column must have a value.

```sql
CREATE TABLE students (
    name VARCHAR(100) NOT NULL
);
```

### CHECK
Validates data before insertion.

```sql
CREATE TABLE courses (
    duration_years INT CHECK (duration_years > 0 AND duration_years <= 6)
);
```

### DEFAULT
Sets default value if none provided.

```sql
CREATE TABLE payment (
    status ENUM('Pending', 'Completed') DEFAULT 'Completed'
);
```

---

## Joins

### INNER JOIN
Returns records with matching values in both tables.

```sql
SELECT 
    s.name,
    c.course_name
FROM students s
INNER JOIN courses c ON s.course_id = c.course_id;
```

**Result:** Only students who have a valid course

### LEFT JOIN (LEFT OUTER JOIN)
Returns all records from left table, matching records from right.

```sql
SELECT 
    s.name,
    p.amount_paid
FROM students s
LEFT JOIN payment p ON s.student_id = p.student_id;
```

**Result:** All students, even those without payments (NULL for payment data)

### RIGHT JOIN
Returns all records from right table, matching records from left.

```sql
SELECT 
    s.name,
    c.course_name
FROM students s
RIGHT JOIN courses c ON s.course_id = c.course_id;
```

**Result:** All courses, even those without students

### Multiple Joins
Joining more than two tables.

```sql
SELECT 
    s.name AS student_name,
    c.course_name,
    f.semester,
    p.amount_paid
FROM payment p
INNER JOIN students s ON p.student_id = s.student_id
INNER JOIN fees_structure f ON p.fee_id = f.fee_id
INNER JOIN courses c ON f.course_id = c.course_id;
```

**Explanation:**
- Joins 4 tables to get complete payment information
- Each JOIN connects two tables via foreign key relationship

---

## Aggregate Functions

### COUNT
Counts number of rows.

```sql
-- Count all students
SELECT COUNT(*) FROM students;

-- Count students in specific course
SELECT COUNT(*) FROM students WHERE course_id = 1;

-- Count distinct courses
SELECT COUNT(DISTINCT course_id) FROM students;
```

### SUM
Calculates total of numeric column.

```sql
-- Total revenue
SELECT SUM(amount_paid) FROM payment WHERE status = 'Completed';

-- Total fees for a course
SELECT SUM(amount) FROM fees_structure WHERE course_id = 1;
```

### AVG
Calculates average value.

```sql
-- Average payment amount
SELECT AVG(amount_paid) FROM payment;

-- Average course fees
SELECT AVG(total_fees) FROM courses;
```

### MAX and MIN
Finds maximum and minimum values.

```sql
-- Highest payment
SELECT MAX(amount_paid) FROM payment;

-- Lowest course fee
SELECT MIN(total_fees) FROM courses;

-- Latest payment date
SELECT MAX(payment_date) FROM payment;
```

### GROUP BY
Groups rows with same values.

```sql
-- Count students per course
SELECT 
    course_id,
    COUNT(*) as student_count
FROM students
GROUP BY course_id;

-- Total payments per student
SELECT 
    student_id,
    SUM(amount_paid) as total_paid
FROM payment
WHERE status = 'Completed'
GROUP BY student_id;
```

### HAVING
Filters grouped results (WHERE for groups).

```sql
-- Courses with more than 2 students
SELECT 
    course_id,
    COUNT(*) as student_count
FROM students
GROUP BY course_id
HAVING COUNT(*) > 2;

-- Students who paid more than 50000
SELECT 
    student_id,
    SUM(amount_paid) as total_paid
FROM payment
WHERE status = 'Completed'
GROUP BY student_id
HAVING SUM(amount_paid) > 50000;
```

**Difference between WHERE and HAVING:**
- WHERE: Filters rows before grouping
- HAVING: Filters groups after aggregation

---

## Subqueries

### Subquery in WHERE Clause

```sql
-- Students who paid more than average
SELECT name, email
FROM students
WHERE student_id IN (
    SELECT student_id 
    FROM payment 
    WHERE amount_paid > (SELECT AVG(amount_paid) FROM payment)
);
```

### Subquery in SELECT Clause

```sql
-- Student with their total payments
SELECT 
    name,
    (SELECT SUM(amount_paid) 
     FROM payment 
     WHERE student_id = s.student_id) as total_paid
FROM students s;
```

### Subquery in FROM Clause

```sql
-- Average of course totals
SELECT AVG(course_total) as overall_avg
FROM (
    SELECT course_id, SUM(total_fees) as course_total
    FROM courses
    GROUP BY course_id
) as course_totals;
```

### EXISTS
Checks if subquery returns any rows.

```sql
-- Students who have made payments
SELECT name
FROM students s
WHERE EXISTS (
    SELECT 1 
    FROM payment p 
    WHERE p.student_id = s.student_id
);
```

---

## Views

### CREATE VIEW
Creates a virtual table based on query.

```sql
CREATE VIEW student_payment_summary AS
SELECT 
    s.student_id,
    s.name,
    c.course_name,
    COUNT(p.payment_id) as total_payments,
    SUM(p.amount_paid) as total_paid,
    c.total_fees,
    (c.total_fees - COALESCE(SUM(p.amount_paid), 0)) as balance_due
FROM students s
JOIN courses c ON s.course_id = c.course_id
LEFT JOIN payment p ON s.student_id = p.student_id AND p.status = 'Completed'
GROUP BY s.student_id;
```

**Benefits:**
- Simplifies complex queries
- Provides data abstraction
- Enhances security (hide sensitive columns)
- Reusable

### Using Views

```sql
-- Query view like a table
SELECT * FROM student_payment_summary;

-- Filter view results
SELECT * FROM student_payment_summary WHERE balance_due > 0;
```

### DROP VIEW

```sql
DROP VIEW IF EXISTS student_payment_summary;
```

---

## Stored Procedures

### CREATE PROCEDURE
Creates reusable SQL code block.

```sql
DELIMITER //

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
    SELECT COUNT(*) INTO course_exists 
    FROM courses 
    WHERE course_id = p_course_id;
    
    IF course_exists = 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Course does not exist';
    END IF;
    
    -- Insert student
    INSERT INTO students (name, email, phone, course_id, admission_year)
    VALUES (p_name, p_email, p_phone, p_course_id, p_admission_year);
END //

DELIMITER ;
```

**Explanation:**
- `DELIMITER //`: Changes delimiter to allow semicolons in procedure
- `IN`: Input parameter
- `DECLARE`: Declares local variable
- `IF...THEN...END IF`: Conditional logic
- `SIGNAL`: Raises error

### CALL Procedure

```sql
CALL add_student('John Doe', 'john@email.com', '1234567890', 1, 2024);
```

### DROP PROCEDURE

```sql
DROP PROCEDURE IF EXISTS add_student;
```

---

## Triggers

### CREATE TRIGGER
Automatically executes code on specific events.

```sql
DELIMITER //

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
```

**Explanation:**
- `AFTER INSERT`: Trigger fires after row insertion
- `FOR EACH ROW`: Executes for each inserted row
- `NEW`: Refers to newly inserted row
- `CONCAT`: Concatenates strings
- `LPAD`: Pads string with zeros on left

### Trigger Types

1. **BEFORE INSERT**: Before row insertion
2. **AFTER INSERT**: After row insertion
3. **BEFORE UPDATE**: Before row update
4. **AFTER UPDATE**: After row update
5. **BEFORE DELETE**: Before row deletion
6. **AFTER DELETE**: After row deletion

### DROP TRIGGER

```sql
DROP TRIGGER IF EXISTS after_payment_insert;
```

---

## Advanced SQL Concepts

### CASE Statement
Conditional logic in queries.

```sql
SELECT 
    name,
    total_fees,
    total_paid,
    CASE 
        WHEN total_paid >= total_fees THEN 'Paid'
        WHEN total_paid >= total_fees * 0.5 THEN 'Partial'
        ELSE 'Pending'
    END as payment_status
FROM student_payment_summary;
```

### COALESCE
Returns first non-NULL value.

```sql
SELECT 
    name,
    COALESCE(SUM(amount_paid), 0) as total_paid
FROM students s
LEFT JOIN payment p ON s.student_id = p.student_id
GROUP BY s.student_id;
```

### DATE Functions

```sql
-- Current date and time
SELECT NOW();
SELECT CURDATE();
SELECT CURTIME();

-- Date formatting
SELECT DATE_FORMAT(payment_date, '%d-%m-%Y') FROM payment;

-- Date arithmetic
SELECT DATE_ADD(payment_date, INTERVAL 30 DAY) as due_date FROM payment;
SELECT DATEDIFF(CURDATE(), payment_date) as days_ago FROM payment;

-- Extract parts
SELECT YEAR(payment_date), MONTH(payment_date), DAY(payment_date) FROM payment;
```

### String Functions

```sql
-- Concatenation
SELECT CONCAT(name, ' - ', email) as student_info FROM students;

-- Uppercase/Lowercase
SELECT UPPER(name), LOWER(email) FROM students;

-- Substring
SELECT SUBSTRING(name, 1, 3) as initials FROM students;

-- Length
SELECT name, LENGTH(name) as name_length FROM students;
```

---

## Transaction Management

### ACID Properties
- **Atomicity**: All or nothing
- **Consistency**: Data remains valid
- **Isolation**: Transactions don't interfere
- **Durability**: Changes are permanent

### Transaction Commands

```sql
-- Start transaction
START TRANSACTION;

-- Execute queries
INSERT INTO payment (...) VALUES (...);
UPDATE students SET ... WHERE ...;

-- Commit (save changes)
COMMIT;

-- Or rollback (undo changes)
ROLLBACK;
```

**Example:**

```sql
START TRANSACTION;

INSERT INTO payment (student_id, fee_id, payment_date, amount_paid, mode, status)
VALUES (1, 1, '2024-01-15', 25000.00, 'UPI', 'Completed');

SET @payment_id = LAST_INSERT_ID();

INSERT INTO receipt (payment_id, receipt_number)
VALUES (@payment_id, CONCAT('RCP2024', LPAD(@payment_id, 6, '0')));

COMMIT;
```

---

## Indexes

### CREATE INDEX
Improves query performance.

```sql
-- Single column index
CREATE INDEX idx_student_name ON students(name);

-- Composite index
CREATE INDEX idx_payment_student_date ON payment(student_id, payment_date);

-- Unique index
CREATE UNIQUE INDEX idx_student_email ON students(email);
```

### SHOW INDEXES

```sql
SHOW INDEXES FROM students;
```

### DROP INDEX

```sql
DROP INDEX idx_student_name ON students;
```

---

## Best Practices

1. **Always use WHERE in UPDATE/DELETE**
   ```sql
   -- Good
   DELETE FROM payment WHERE payment_id = 100;
   
   -- Bad (deletes everything!)
   DELETE FROM payment;
   ```

2. **Use transactions for related operations**
   ```sql
   START TRANSACTION;
   -- multiple related queries
   COMMIT;
   ```

3. **Index frequently queried columns**
   ```sql
   CREATE INDEX idx_email ON students(email);
   ```

4. **Use JOINs instead of subqueries when possible**
   ```sql
   -- Better performance
   SELECT s.name, c.course_name
   FROM students s
   JOIN courses c ON s.course_id = c.course_id;
   ```

5. **Avoid SELECT * in production**
   ```sql
   -- Good
   SELECT name, email FROM students;
   
   -- Avoid
   SELECT * FROM students;
   ```

6. **Use LIMIT for large result sets**
   ```sql
   SELECT * FROM payment ORDER BY payment_date DESC LIMIT 100;
   ```

7. **Validate data with CHECK constraints**
   ```sql
   CHECK (amount > 0)
   CHECK (phone REGEXP '^[0-9]{10}$')
   ```

---

## Common SQL Patterns

### Pagination

```sql
-- Page 1 (records 1-10)
SELECT * FROM students LIMIT 0, 10;

-- Page 2 (records 11-20)
SELECT * FROM students LIMIT 10, 10;

-- Page 3 (records 21-30)
SELECT * FROM students LIMIT 20, 10;
```

### Find Duplicates

```sql
SELECT email, COUNT(*) as count
FROM students
GROUP BY email
HAVING COUNT(*) > 1;
```

### Ranking

```sql
SELECT 
    name,
    amount_paid,
    RANK() OVER (ORDER BY amount_paid DESC) as payment_rank
FROM payment;
```

### Running Total

```sql
SELECT 
    payment_date,
    amount_paid,
    SUM(amount_paid) OVER (ORDER BY payment_date) as running_total
FROM payment;
```

---

This comprehensive guide covers all major SQL concepts used in the College Fees Management System!
