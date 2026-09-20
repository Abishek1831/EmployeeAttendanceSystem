# Setup Guide (XAMPP)

Step-by-step instructions to get the Employee Attendance Management System
running locally.

## Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any Apache + PHP + MySQL stack)
  with PHP 7.4+ and MySQL/MariaDB.
- A web browser.

## 1. Get the project into XAMPP's web root

Clone or download this repository into XAMPP's `htdocs` folder:

```
C:\xampp\htdocs\employee-attendance-system   (Windows)
/Applications/XAMPP/htdocs/employee-attendance-system   (macOS)
/opt/lampp/htdocs/employee-attendance-system   (Linux)
```

## 2. Start Apache and MySQL

Open the XAMPP Control Panel and start **Apache** and **MySQL**.

## 3. Create the database

This repository does **not** include a `.sql` dump (see
[`database/README.md`](../database/README.md) for why and for the full schema
reference). You'll need to create it yourself:

1. Open [phpMyAdmin](http://localhost/phpmyadmin).
2. Create a new database named exactly:
   ```
   employee_attendance
   ```
3. Create the following tables, matching the column names documented in
   [`database/README.md`](../database/README.md):
   - `admin`
   - `employees`
   - `departments`
   - `shifts`
   - `employee_shifts`
   - `attendance`
   - `leave_applications`
   - `leave_balance`
4. Insert at least one admin row and a few departments/shifts so you have
   something to log in with and assign, e.g.:
   ```sql
   INSERT INTO admin (username, password) VALUES ('admin', MD5('admin123'));
   INSERT INTO departments (dept_name) VALUES ('Engineering'), ('HR'), ('Sales');
   INSERT INTO shifts (shift_name, start_time, end_time, grace_period)
     VALUES ('General Shift', '09:00:00', '18:00:00', 15);
   ```
5. Once you have a working setup, consider exporting it
   (`Export` tab in phpMyAdmin, or `mysqldump`) and committing it as
   `database/employee_attendance.sql` so future setups are one step, not five.

## 4. Configure the database connection

Open [`config/config.php`](../config/config.php) and confirm the credentials
match your MySQL setup. The defaults match a fresh XAMPP install:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'employee_attendance');
```

If your MySQL root user has a password, or you're using a different database
name, update these values accordingly.

## 5. Verify the setup (optional but recommended)

Browse to:

```
http://localhost/employee-attendance-system/tools/test_system.php
```

This will check the database connection, confirm each table exists, and show
whether your admin/employee accounts and demo data are in place. See
[`tools/README.md`](../tools/README.md) — **do not leave this folder deployed
anywhere but your own local machine.**

## 6. Log in

Open:

```
http://localhost/employee-attendance-system/
```

Log in as admin with the account you created (e.g. `admin` / `admin123` if you
used the example insert above), or as an employee once you've registered one
through the admin dashboard.

## Troubleshooting

| Problem | Likely cause |
|---|---|
| Blank page or "Database connection failed" | MySQL isn't running, or the credentials in `config/config.php` are wrong |
| "Invalid admin/employee credentials" | The account doesn't exist yet, or the password doesn't match what's stored (remember passwords are hashed with `MD5()`) |
| Dropdowns empty on the registration form | No rows in `departments` or `shifts` yet |
| Redirected straight back to the login page | Your session expired, or you tried to open an admin page as an employee (or vice versa) |
