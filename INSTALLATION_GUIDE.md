# Installation Guide - College Fees Management System

## Prerequisites

1. **XAMPP** (Apache + MySQL + PHP)
   - Download from: https://www.apachefriends.org/
   - Version: 7.4 or higher recommended

2. **Web Browser**
   - Chrome, Firefox, Edge, or Safari

3. **Text Editor** (Optional, for code viewing)
   - VS Code, Sublime Text, or Notepad++

---

## Step-by-Step Installation

### Step 1: Install XAMPP

1. Download XAMPP installer for your operating system
2. Run the installer
3. Select components:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
4. Choose installation directory (default: `C:\xampp`)
5. Complete the installation

### Step 2: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Click **Start** for:
   - Apache (Web Server)
   - MySQL (Database Server)
3. Verify both services show "Running" status
4. Default ports:
   - Apache: 80
   - MySQL: 3306

**Troubleshooting:**
- If port 80 is busy, change Apache port in `httpd.conf`
- If MySQL port is busy, change in `my.ini`

### Step 3: Copy Project Files

1. Navigate to XAMPP installation directory
2. Open the `htdocs` folder: `C:\xampp\htdocs\`
3. Copy the entire `college_fees` folder into `htdocs`
4. Final path should be: `C:\xampp\htdocs\college_fees\`

### Step 4: Create Database

**Method 1: Using phpMyAdmin (Recommended)**

1. Open browser and go to: `http://localhost/phpmyadmin`
2. Click on **"New"** in the left sidebar
3. Database name: `college_fees_db`
4. Collation: `utf8_general_ci`
5. Click **"Create"**

**Method 2: Using SQL Command**

1. In phpMyAdmin, click on **"SQL"** tab
2. Paste: `CREATE DATABASE college_fees_db;`
3. Click **"Go"**

### Step 5: Import Database Schema

1. In phpMyAdmin, select `college_fees_db` from left sidebar
2. Click on **"Import"** tab
3. Click **"Choose File"**
4. Navigate to: `C:\xampp\htdocs\college_fees\database\schema.sql`
5. Click **"Go"** to execute
6. Wait for success message: "Import has been successfully finished"

**What this does:**
- Creates 6 tables (courses, students, fees_structure, admin, payment, receipt)
- Sets up foreign keys and constraints
- Creates indexes for performance
- Creates views for reporting
- Creates stored procedures
- Creates triggers

### Step 6: Import Sample Data

1. Still in phpMyAdmin with `college_fees_db` selected
2. Click on **"Import"** tab again
3. Choose file: `C:\xampp\htdocs\college_fees\database\sample_data.sql`
4. Click **"Go"**
5. Verify data insertion success

**Sample data includes:**
- 6 courses
- 10 students
- 33 fee structure entries
- 3 admin accounts
- 20 payment records
- Auto-generated receipts

### Step 7: Verify Database Setup

1. In phpMyAdmin, click on `college_fees_db`
2. You should see 6 tables:
   - admin
   - courses
   - fees_structure
   - payment
   - receipt
   - students
3. Click on each table and verify data exists

### Step 8: Access the Application

1. Open web browser
2. Go to: `http://localhost/college_fees/`
3. You should be redirected to login page
4. Use default credentials:
   - **Username:** `admin`
   - **Password:** `admin123`

### Step 9: Test the System

After logging in, test these features:

1. **Dashboard**
   - View statistics
   - Check recent payments
   - Review course-wise revenue

2. **Add Student**
   - Navigate to: Students → Add Student
   - Fill in details and submit
   - Verify student appears in "View Students"

3. **Make Payment**
   - Navigate to: Payments → Make Payment
   - Select a student
   - Choose semester
   - Record payment
   - View generated receipt

4. **View Reports**
   - Check all students with payment status
   - Filter payments by status/mode
   - View fee structures

---

## Configuration

### Database Connection Settings

File: `config/db_connect.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'college_fees_db');
```

**Change these if:**
- MySQL is on different host
- You set a root password
- You used different database name

### Admin Accounts

Default admin accounts (from sample_data.sql):

| Username  | Password      | Role                |
|-----------|---------------|---------------------|
| admin     | admin123      | System Administrator|
| accounts  | accounts123   | Accounts Manager    |
| registrar | registrar123  | Registrar Office    |

**Security Note:** Change passwords in production!

---

## Directory Structure

```
college_fees/
├── config/
│   └── db_connect.php          # Database connection
├── database/
│   ├── schema.sql              # Database structure
│   ├── sample_data.sql         # Sample data
│   ├── queries.sql             # Example queries
│   └── ER_DIAGRAM.md           # ER diagram documentation
├── admin/
│   ├── login.php               # Admin login
│   └── logout.php              # Logout handler
├── students/
│   ├── add_student.php         # Add new student
│   └── view_students.php       # View all students
├── courses/
│   ├── manage_courses.php      # Manage courses
│   └── view_fees_structure.php # Fee structure
├── payments/
│   ├── make_payment.php        # Record payment
│   ├── view_payments.php       # View all payments
│   ├── generate_receipt.php    # Generate receipt
│   └── get_fee_structure.php   # AJAX endpoint
├── includes/
│   ├── header.php              # Common header
│   └── footer.php              # Common footer
├── index.php                   # Dashboard
├── README.md                   # Project overview
└── INSTALLATION_GUIDE.md       # This file
```

---

## Common Issues and Solutions

### Issue 1: "Connection failed" error
**Solution:**
- Verify MySQL is running in XAMPP Control Panel
- Check database credentials in `config/db_connect.php`
- Ensure database `college_fees_db` exists

### Issue 2: "Table doesn't exist" error
**Solution:**
- Import `schema.sql` first
- Then import `sample_data.sql`
- Check phpMyAdmin for table existence

### Issue 3: Login not working
**Solution:**
- Verify `admin` table has data
- Check username/password (case-sensitive)
- Clear browser cache and cookies

### Issue 4: Receipt not generating
**Solution:**
- Check if trigger `after_payment_insert` exists
- Verify payment status is 'Completed'
- Check `receipt` table for entries

### Issue 5: Foreign key constraint errors
**Solution:**
- Import schema.sql in correct order
- Don't modify table structure manually
- Use provided SQL scripts

### Issue 6: Apache won't start
**Solution:**
- Check if port 80 is used by another application
- Stop IIS or Skype if running
- Change Apache port in XAMPP config

### Issue 7: MySQL won't start
**Solution:**
- Check if port 3306 is available
- Stop other MySQL instances
- Check XAMPP error logs

---

## Testing Queries

After installation, test these SQL queries in phpMyAdmin:

### 1. View all students with payment status
```sql
SELECT * FROM student_payment_summary;
```

### 2. Check recent payments
```sql
SELECT * FROM payment_receipt_details 
ORDER BY payment_date DESC 
LIMIT 10;
```

### 3. Course-wise revenue
```sql
SELECT 
    c.course_name,
    COUNT(s.student_id) as students,
    SUM(p.amount_paid) as revenue
FROM courses c
LEFT JOIN students s ON c.course_id = s.course_id
LEFT JOIN payment p ON s.student_id = p.student_id
WHERE p.status = 'Completed'
GROUP BY c.course_id;
```

### 4. Pending payments
```sql
SELECT * FROM payment WHERE status = 'Pending';
```

---

## Security Recommendations

### For Production Use:

1. **Change Default Passwords**
   ```sql
   UPDATE admin 
   SET password = MD5('new_secure_password') 
   WHERE username = 'admin';
   ```

2. **Use Password Hashing**
   - Replace MD5 with `password_hash()` and `password_verify()`
   - Update login.php accordingly

3. **Enable HTTPS**
   - Get SSL certificate
   - Configure Apache for HTTPS

4. **Restrict Database Access**
   - Create separate MySQL user (not root)
   - Grant only necessary privileges

5. **Input Validation**
   - Already implemented with `sanitize_input()`
   - Add more validation as needed

6. **Session Security**
   - Set secure session parameters
   - Implement session timeout

7. **Backup Database**
   - Regular automated backups
   - Store backups securely

---

## Backup and Restore

### Backup Database

**Method 1: phpMyAdmin**
1. Select `college_fees_db`
2. Click "Export"
3. Choose "Quick" or "Custom"
4. Click "Go"
5. Save the .sql file

**Method 2: Command Line**
```bash
cd C:\xampp\mysql\bin
mysqldump -u root college_fees_db > backup.sql
```

### Restore Database

**Method 1: phpMyAdmin**
1. Select `college_fees_db`
2. Click "Import"
3. Choose backup file
4. Click "Go"

**Method 2: Command Line**
```bash
cd C:\xampp\mysql\bin
mysql -u root college_fees_db < backup.sql
```

---

## Performance Optimization

1. **Enable Query Cache** (my.ini)
   ```ini
   query_cache_type = 1
   query_cache_size = 64M
   ```

2. **Optimize Tables** (phpMyAdmin)
   - Select all tables
   - Choose "Optimize table" from dropdown

3. **Add More Indexes** (if needed)
   ```sql
   CREATE INDEX idx_name ON students(name);
   ```

---

## Support and Documentation

- **Database Schema:** See `database/ER_DIAGRAM.md`
- **Sample Queries:** See `database/queries.sql`
- **Project Overview:** See `README.md`

---

## Next Steps

1. ✅ Complete installation
2. ✅ Test all features
3. ✅ Review database design
4. ✅ Understand SQL queries
5. ✅ Customize as needed
6. ✅ Add more features
7. ✅ Deploy to production (with security measures)

---

## Uninstallation

To remove the system:

1. Delete project folder: `C:\xampp\htdocs\college_fees\`
2. Drop database in phpMyAdmin:
   ```sql
   DROP DATABASE college_fees_db;
   ```
3. (Optional) Uninstall XAMPP

---

**Installation Complete! 🎉**

Access your system at: `http://localhost/college_fees/`
