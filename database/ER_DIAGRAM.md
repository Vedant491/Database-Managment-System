# Entity-Relationship Diagram (ERD)

## College Fees Management System - Database Design

### Entities and Attributes

#### 1. COURSES
**Primary Key:** course_id
- course_id (INT, PK, AUTO_INCREMENT)
- course_name (VARCHAR(100), NOT NULL, UNIQUE)
- duration_years (INT, NOT NULL, CHECK: 1-6)
- total_fees (DECIMAL(10,2), NOT NULL, CHECK: > 0)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

#### 2. STUDENTS
**Primary Key:** student_id
**Foreign Key:** course_id → COURSES(course_id)
- student_id (INT, PK, AUTO_INCREMENT)
- name (VARCHAR(100), NOT NULL)
- email (VARCHAR(100), NOT NULL, UNIQUE)
- phone (VARCHAR(15), NOT NULL, CHECK: 10-15 digits)
- course_id (INT, FK, NOT NULL)
- admission_year (YEAR, NOT NULL)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

#### 3. FEES_STRUCTURE
**Primary Key:** fee_id
**Foreign Key:** course_id → COURSES(course_id)
- fee_id (INT, PK, AUTO_INCREMENT)
- course_id (INT, FK, NOT NULL)
- semester (INT, NOT NULL, CHECK: 1-12)
- amount (DECIMAL(10,2), NOT NULL, CHECK: > 0)
- description (VARCHAR(200))
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- **UNIQUE CONSTRAINT:** (course_id, semester)

#### 4. ADMIN
**Primary Key:** admin_id
- admin_id (INT, PK, AUTO_INCREMENT)
- username (VARCHAR(50), NOT NULL, UNIQUE)
- password (VARCHAR(255), NOT NULL)
- full_name (VARCHAR(100), NOT NULL)
- email (VARCHAR(100), NOT NULL, UNIQUE)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- last_login (TIMESTAMP, NULL)

#### 5. PAYMENT
**Primary Key:** payment_id
**Foreign Keys:** 
- student_id → STUDENTS(student_id)
- fee_id → FEES_STRUCTURE(fee_id)
- payment_id (INT, PK, AUTO_INCREMENT)
- student_id (INT, FK, NOT NULL)
- fee_id (INT, FK, NOT NULL)
- payment_date (DATE, NOT NULL)
- amount_paid (DECIMAL(10,2), NOT NULL, CHECK: > 0)
- mode (ENUM: 'Cash', 'Card', 'UPI', 'Net Banking', 'Cheque')
- status (ENUM: 'Pending', 'Completed', 'Failed', 'Refunded', DEFAULT: 'Completed')
- transaction_id (VARCHAR(100))
- remarks (TEXT)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

#### 6. RECEIPT
**Primary Key:** receipt_id
**Foreign Key:** payment_id → PAYMENT(payment_id)
- receipt_id (INT, PK, AUTO_INCREMENT)
- payment_id (INT, FK, NOT NULL, UNIQUE)
- generated_date (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- receipt_number (VARCHAR(50), NOT NULL, UNIQUE)

---

## Relationships

### 1. COURSES → STUDENTS (One-to-Many)
- **Cardinality:** 1:M
- **Relationship:** "offers enrollment to"
- **Description:** One course can have many students enrolled
- **Foreign Key:** students.course_id REFERENCES courses.course_id
- **Constraints:** 
  - ON DELETE RESTRICT (Cannot delete course if students exist)
  - ON UPDATE CASCADE (Updates propagate to students)

### 2. COURSES → FEES_STRUCTURE (One-to-Many)
- **Cardinality:** 1:M
- **Relationship:** "has fee structure for"
- **Description:** One course has multiple semester-wise fee structures
- **Foreign Key:** fees_structure.course_id REFERENCES courses.course_id
- **Constraints:**
  - ON DELETE CASCADE (Deleting course removes its fee structure)
  - ON UPDATE CASCADE (Updates propagate to fee structure)

### 3. STUDENTS → PAYMENT (One-to-Many)
- **Cardinality:** 1:M
- **Relationship:** "makes payments"
- **Description:** One student can make multiple payments
- **Foreign Key:** payment.student_id REFERENCES students.student_id
- **Constraints:**
  - ON DELETE CASCADE (Deleting student removes their payments)
  - ON UPDATE CASCADE (Updates propagate to payments)

### 4. FEES_STRUCTURE → PAYMENT (One-to-Many)
- **Cardinality:** 1:M
- **Relationship:** "is paid through"
- **Description:** One fee structure entry can have multiple payments
- **Foreign Key:** payment.fee_id REFERENCES fees_structure.fee_id
- **Constraints:**
  - ON DELETE RESTRICT (Cannot delete fee structure if payments exist)
  - ON UPDATE CASCADE (Updates propagate to payments)

### 5. PAYMENT → RECEIPT (One-to-One)
- **Cardinality:** 1:1
- **Relationship:** "generates"
- **Description:** Each completed payment generates exactly one receipt
- **Foreign Key:** receipt.payment_id REFERENCES payment.payment_id
- **Constraints:**
  - UNIQUE constraint on payment_id
  - ON DELETE CASCADE (Deleting payment removes its receipt)
  - ON UPDATE CASCADE (Updates propagate to receipt)

### 6. ADMIN (Independent Entity)
- **Cardinality:** N/A
- **Relationship:** None (Independent)
- **Description:** Stores admin credentials for system access
- **No Foreign Keys**

---

## Visual Representation

```
┌─────────────────┐
│    COURSES      │
│─────────────────│
│ PK: course_id   │
│     course_name │
│ duration_years  │
│    total_fees   │
└────────┬────────┘
         │ 1
         │
         │ M
    ┌────┴─────────────────────┐
    │                          │
┌───▼──────────┐      ┌────────▼──────────┐
│   STUDENTS   │      │  FEES_STRUCTURE   │
│──────────────│      │───────────────────│
│PK:student_id │      │ PK: fee_id        │
│     name     │      │ FK: course_id     │
│     email    │      │     semester      │
│     phone    │      │     amount        │
│FK:course_id  │      │   description     │
│admission_year│      └────────┬──────────┘
└──────┬───────┘               │ 1
       │ 1                     │
       │                       │
       │ M                     │ M
       │              ┌────────▼──────────┐
       └──────────────►    PAYMENT        │
                      │───────────────────│
                      │ PK: payment_id    │
                      │ FK: student_id    │
                      │ FK: fee_id        │
                      │  payment_date     │
                      │  amount_paid      │
                      │     mode          │
                      │    status         │
                      │ transaction_id    │
                      └────────┬──────────┘
                               │ 1
                               │
                               │ 1
                      ┌────────▼──────────┐
                      │     RECEIPT       │
                      │───────────────────│
                      │ PK: receipt_id    │
                      │ FK: payment_id    │
                      │ generated_date    │
                      │ receipt_number    │
                      └───────────────────┘

┌─────────────────┐
│     ADMIN       │  (Independent Entity)
│─────────────────│
│ PK: admin_id    │
│    username     │
│    password     │
│   full_name     │
│     email       │
│   last_login    │
└─────────────────┘
```

---

## Normalization Analysis

### First Normal Form (1NF)
✅ All tables satisfy 1NF:
- All attributes contain atomic values
- No repeating groups
- Each column has a unique name
- Order of rows doesn't matter

### Second Normal Form (2NF)
✅ All tables satisfy 2NF:
- Already in 1NF
- All non-key attributes are fully dependent on the primary key
- No partial dependencies exist

### Third Normal Form (3NF)
✅ All tables satisfy 3NF:
- Already in 2NF
- No transitive dependencies
- All non-key attributes depend only on the primary key

**Example:**
- In STUDENTS table: name, email, phone depend only on student_id
- course_name is NOT stored in STUDENTS (would be transitive dependency)
- Instead, course_id (FK) is used to reference COURSES table

---

## Constraints Summary

### Primary Key Constraints
- All tables have AUTO_INCREMENT primary keys
- Ensures unique identification of each record

### Foreign Key Constraints
- **students.course_id** → courses.course_id
- **fees_structure.course_id** → courses.course_id
- **payment.student_id** → students.student_id
- **payment.fee_id** → fees_structure.fee_id
- **receipt.payment_id** → payment.payment_id

### Unique Constraints
- courses.course_name (UNIQUE)
- students.email (UNIQUE)
- admin.username (UNIQUE)
- admin.email (UNIQUE)
- fees_structure(course_id, semester) (COMPOSITE UNIQUE)
- receipt.payment_id (UNIQUE)
- receipt.receipt_number (UNIQUE)

### Check Constraints
- courses.duration_years: 1-6 years
- courses.total_fees: > 0
- students.phone: 10-15 digits
- fees_structure.semester: 1-12
- fees_structure.amount: > 0
- payment.amount_paid: > 0

### NOT NULL Constraints
- All primary keys
- All foreign keys
- Essential attributes (name, email, phone, etc.)

### Default Values
- created_at: CURRENT_TIMESTAMP
- payment.status: 'Completed'
- receipt.generated_date: CURRENT_TIMESTAMP

### Referential Integrity Actions
- **ON DELETE CASCADE:** Deletes propagate (courses→fees, students→payments, payments→receipts)
- **ON DELETE RESTRICT:** Prevents deletion if references exist (courses→students, fees→payments)
- **ON UPDATE CASCADE:** Updates propagate to all related tables

---

## Indexes for Performance

```sql
-- Automatically created on Primary Keys and Unique constraints
-- Additional indexes:
CREATE INDEX idx_student_course ON students(course_id);
CREATE INDEX idx_student_email ON students(email);
CREATE INDEX idx_payment_student ON payment(student_id);
CREATE INDEX idx_payment_date ON payment(payment_date);
CREATE INDEX idx_payment_status ON payment(status);
CREATE INDEX idx_fees_course ON fees_structure(course_id);
```

---

## Database Views

### 1. student_payment_summary
Provides quick overview of each student's payment status

### 2. payment_receipt_details
Combines payment and receipt information for reporting

---

## Triggers

### after_payment_insert
- Automatically generates receipt when payment status is 'Completed'
- Creates unique receipt number: RCP{YEAR}{PAYMENT_ID}

---

## Stored Procedures

### 1. add_student()
- Validates course existence before adding student
- Parameters: name, email, phone, course_id, admission_year

### 2. record_payment()
- Records payment and generates receipt in single transaction
- Parameters: student_id, fee_id, payment_date, amount_paid, mode, transaction_id
- Returns: payment_id and receipt_number
