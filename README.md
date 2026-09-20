# Employee Attendance Management System

A web-based system for managing employee attendance, leave, departments, and
shifts, built with **PHP** and **MySQL**. Built as a DBMS course project.

## Overview

Companies need a simple, reliable way to track when employees are working, let
employees request time off, and give admins visibility into both — without
paper registers or spreadsheets. This project implements that as a small,
self-contained PHP + MySQL web app: employees check in/out and apply for
leave from their own dashboard, while admins get a live overview of today's
attendance, manage leave approvals, and register new employees.

It's a monolithic PHP application by design — plain PHP pages that talk
directly to MySQL via `mysqli`, with no separate frontend framework or REST
API layer, other than small JSON endpoints used for AJAX actions
(check-in/out, login, leave actions). This keeps the project easy to read,
run, and grade.

## Key Features

- Separate login flows for **Admin** and **Employee**, from one login page
- Live admin dashboard: today's attendance, pending leave requests, quick
  stats (present / absent / on leave)
- Employee dashboard: one-click check-in/check-out with automatic
  **on-time vs. late** detection based on assigned shift
- Leave application workflow with automatic balance checks and admin
  approval/rejection
- Employee registration with department and shift assignment
- Monthly attendance statistics per employee

See [`docs/FEATURES.md`](docs/FEATURES.md) for the full feature breakdown.

### Admin features
- Dashboard with live stats: total employees, present today, on leave, absent
- View today's full attendance list across all employees
- Approve or reject pending leave requests (auto-adjusts leave balance)
- Register new employees with department, shift, and initial leave balance

### Employee features
- Check in / check out for the day, with late detection against your shift
- View your monthly attendance summary (present, absent, late days)
- Apply for leave (sick / casual / earned) with automatic day-count and
  balance validation
- View your full leave request history and its status

## Attendance Workflow

1. Employee logs in and clicks **Check In**.
2. The system compares the check-in time to their assigned shift's start
   time + grace period to mark the day `present` or `late`.
3. At the end of the day, the employee clicks **Check Out**; worked hours are
   calculated automatically.
4. Admins see this activity live on the admin dashboard as it happens.

## Leave Management

1. Employee opens **Apply for Leave**, picks a leave type, start/end date,
   and a reason.
2. The system calculates the number of days and checks it against the
   employee's remaining balance for that leave type before allowing the
   request.
3. The request appears in the admin's pending queue.
4. Admin approves or rejects it; on approval, the days are deducted from the
   employee's leave balance automatically.

## Department & Shift Management

- Employees belong to a **department**, which is shown throughout the admin
  and employee views.
- Employees are assigned a **shift** (start time, end time, and a grace
  period), which determines whether a check-in counts as on-time or late.
- Both are set up directly in the database and selected from dropdowns when
  registering an employee — see [`database/README.md`](database/README.md)
  for the full schema.

## Technology Stack

| Layer | Technology |
|---|---|
| Backend | PHP (procedural, `mysqli`) |
| Database | MySQL / MariaDB |
| Frontend | HTML, CSS, vanilla JavaScript (embedded per page) |
| Communication | Fetch API calls to PHP endpoints returning JSON |
| Local environment | XAMPP (Apache + MySQL + PHP) |

## Database / DBMS Concepts Used

This project was built as a DBMS course project, so it deliberately exercises
core relational database concepts:

- **Relational schema design** across 8 related tables (employees,
  departments, shifts, attendance, leave applications, leave balances, admin)
- **Primary and foreign keys** linking employees to departments, shifts,
  attendance records, and leave data
- **Joins** across multiple tables (e.g. attendance + employees +
  departments for the admin dashboard)
- **Aggregate functions** (`COUNT`, conditional `COUNT(CASE WHEN ...)`) for
  live statistics
- **Date/time functions** (`CURDATE()`, `YEAR()`, `DATE_FORMAT()`,
  `TIMESTAMPDIFF()`) for attendance and leave-year logic
- **Transactional-style workflows**: leave approval updates two tables
  (`leave_applications` and `leave_balance`) as one logical operation

See [`database/README.md`](database/README.md) for the full table-by-table
schema documentation.

## System Architecture

This is a **monolithic PHP + MySQL web application** — not a separate
frontend/backend setup. Each PHP file either renders a full HTML page or acts
as a small JSON API endpoint for AJAX calls from that page's JavaScript.

```
Browser (HTML/CSS/JS)
        │  fetch() calls
        ▼
PHP pages (index.php, admin_dashboard.php, employee_dashboard.php, ...)
        │  mysqli
        ▼
MySQL database (employee_attendance)
```

## Project Structure

```
employee-attendance-system/
├── index.php                 # Login page (admin/employee toggle)
├── login.php                 # AJAX login endpoint (JSON)
├── logout.php                # Destroys session, redirects to login
├── admin_dashboard.php        # Admin dashboard (stats, attendance, leave queue)
├── employee_dashboard.php     # Employee dashboard (check-in/out, stats)
├── employee_leave.php         # Employee leave application + history page
├── register_employee.php      # Admin-only employee registration form
├── employee_api.php           # AJAX endpoint: register/list/update employees
├── attendance.php             # AJAX endpoint: check-in/out, stats
├── leave.php                  # AJAX endpoint: apply/approve/reject leave
├── config/
│   └── config.php             # DB connection settings + session bootstrap
├── database/
│   └── README.md              # Full schema documentation (no dump included)
├── docs/
│   ├── FEATURES.md            # Detailed feature documentation
│   └── SETUP.md                # Step-by-step XAMPP setup guide
├── tools/                     # Dev-only diagnostic scripts (see tools/README.md)
│   ├── db_test.php
│   ├── test.php
│   └── test_system.php
├── README.md
├── SECURITY.md
├── LICENSE
└── .gitignore
```

> **Note:** Styling and client-side JavaScript are currently embedded directly
> in each PHP page (`<style>`/`<script>` blocks) rather than in separate
> `assets/css` or `assets/js` files, since that's how the original project was
> built. Extracting shared CSS/JS into an `assets/` folder is listed under
> **Future Improvements** below.

## Installation / Setup

Full step-by-step instructions (including database setup) are in
[`docs/SETUP.md`](docs/SETUP.md). Quick version:

1. Copy this project into your XAMPP `htdocs` folder.
2. Start Apache and MySQL from the XAMPP control panel.
3. Create a `employee_attendance` database in phpMyAdmin and create the
   tables listed in [`database/README.md`](database/README.md) (no SQL dump
   is included — see that file for why).
4. Check the credentials in [`config/config.php`](config/config.php) match
   your local MySQL setup.
5. Visit `http://localhost/employee-attendance-system/` in your browser.

## Screenshots

_Add screenshots of the login page, admin dashboard, and employee dashboard
here once available._

| Login | Admin Dashboard | Employee Dashboard |
|---|---|---|
| _screenshot placeholder_ | _screenshot placeholder_ | _screenshot placeholder_ |

## Future Improvements

- Migrate password hashing from `MD5()` to `password_hash()` /
  `password_verify()` (see [SECURITY.md](SECURITY.md))
- Move inline `<style>` and `<script>` blocks into shared files under an
  `assets/css` and `assets/js` folder
- Move remaining SQL string interpolation to `mysqli` prepared statements
- Add CSRF tokens to state-changing forms/AJAX calls
- Add server-side validation matching the existing client-side HTML
  validation
- Add a proper `database/` SQL dump/migration script so the project can be
  set up in one step
- Add pagination to the admin attendance/leave lists as data grows
- Add automated tests

## Security Notes

This project went through a security review before publishing. A summary of
what was found and fixed — and what's intentionally left as a known
limitation for a student project — is documented in
[**SECURITY.md**](SECURITY.md). Please read it before using this project as a
base for anything beyond local learning/demo purposes.

## Author

**Abishek D**


