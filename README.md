# College Fees Management System

A complete DBMS project using XAMPP, PHP, and MySQL for managing college fees, students, courses, and payments.

## Database Design

### ER Diagram Description

**Entities and Relationships:**
- COURSE (1) ----< (M) STUDENT
- COURSE (1) ----< (M) FEES_STRUCTURE
- STUDENT (1) ----< (M) PAYMENT
- FEES_STRUCTURE (1) ----< (M) PAYMENT
- PAYMENT (1) ---- (1) RECEIPT
- ADMIN (independent entity for authentication)

### Normalization
- All tables are in 3NF (Third Normal Form)
- No transitive dependencies
- Each non-key attribute depends only on the primary key

## Installation Steps

1. Install XAMPP
2. Start Apache and MySQL services
3. Copy the `college_fees` folder to `C:\xampp\htdocs\`
4. Open phpMyAdmin: `http://localhost/phpmyadmin`
5. Create database: `college_fees_db`
6. Import SQL: Run `database/schema.sql` then `database/sample_data.sql`
7. Access application: `http://localhost/college_fees/`

## Project Structure

```
college_fees/
├── config/
│   └── db_connect.php
├── database/
│   ├── schema.sql
│   ├── sample_data.sql
│   └── queries.sql
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   └── logout.php
├── students/
│   ├── add_student.php
│   ├── view_students.php
│   └── edit_student.php
├── courses/
│   ├── manage_courses.php
│   └── view_fees_structure.php
├── payments/
│   ├── make_payment.php
│   ├── view_payments.php
│   └── generate_receipt.php
├── includes/
│   ├── header.php
│   └── footer.php
└── index.php
```

## Features

- Student Management (Add, View, Edit)
- Course and Fees Structure Management
- Payment Recording with Multiple Modes
- Receipt Generation
- Admin Authentication
- Payment Status Tracking
- Comprehensive Reports

## Default Admin Credentials

Username: `admin`
Password: `admin123`
