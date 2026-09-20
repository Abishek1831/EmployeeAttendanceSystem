# Database

## ⚠️ No SQL dump included

The uploaded project did not contain a `.sql` export, so **no database dump is
included in this repository**. The schema below was reconstructed by reading every
query in the PHP code (`SELECT`, `INSERT`, `UPDATE`) and documenting the tables and
columns the application actually uses.

Before anyone else can run this project, someone needs to:

1. Create a MySQL database named `employee_attendance`.
2. Create the tables described below (matching column names exactly, since the PHP
   code references them directly).
3. Export that database with `mysqldump` or phpMyAdmin and commit it here as
   `database/employee_attendance.sql`, **or** write a `CREATE TABLE` schema script
   and commit that instead.

See [`docs/SETUP.md`](../docs/SETUP.md) for full setup steps.

## Database name

```
employee_attendance
```

## Tables used by the application

These are the tables referenced in the code. Column names are exactly as used in
queries; data types are inferred from usage and are a reasonable starting point, not
a guarantee — adjust as needed when you create the schema.

### `admin`
Stores administrator login accounts.

| Column | Notes |
|---|---|
| `admin_id` | Primary key |
| `username` | Used for login |
| `password` | Stored as `MD5(password)` — see [SECURITY.md](../SECURITY.md) |

### `employees`
Stores employee records.

| Column | Notes |
|---|---|
| `emp_id` | Primary key |
| `emp_code` | Unique employee code (e.g. `EMP001`), used for login |
| `first_name`, `last_name` | Employee name |
| `email` | Must be unique |
| `phone` | Phone number |
| `dept_id` | Foreign key → `departments.dept_id` |
| `password` | Stored as `MD5(password)` — see [SECURITY.md](../SECURITY.md) |
| `status` | `active`, `inactive`, or `on_leave` |
| `joining_date` | Date employee joined |

### `departments`
Stores company departments.

| Column | Notes |
|---|---|
| `dept_id` | Primary key |
| `dept_name` | Department name |

### `shifts`
Stores defined work shifts.

| Column | Notes |
|---|---|
| `shift_id` | Primary key |
| `shift_name` | Shift name (e.g. "Morning") |
| `start_time` | Shift start time |
| `end_time` | Shift end time |
| `grace_period` | Minutes of grace before a check-in counts as "late" |

### `employee_shifts`
Assigns a shift to an employee, with an effective date.

| Column | Notes |
|---|---|
| `emp_id` | Foreign key → `employees.emp_id` |
| `shift_id` | Foreign key → `shifts.shift_id` |
| `effective_date` | Date the assignment takes effect |

### `attendance`
Daily check-in/check-out records.

| Column | Notes |
|---|---|
| `emp_id` | Foreign key → `employees.emp_id` |
| `date` | Attendance date |
| `check_in` | Check-in time |
| `check_out` | Check-out time (nullable until checkout) |
| `status` | `present`, `late`, `absent`, or `on_leave` |
| `work_hours` | Computed on check-out (`TIMESTAMPDIFF(HOUR, check_in, check_out)`) |

### `leave_applications`
Employee leave requests and their approval status.

| Column | Notes |
|---|---|
| `leave_id` | Primary key |
| `emp_id` | Foreign key → `employees.emp_id` |
| `leave_type` | `sick`, `casual`, or `earned` |
| `start_date`, `end_date` | Leave date range |
| `days_count` | Number of days requested |
| `reason` | Employee-provided reason |
| `status` | `pending`, `approved`, or `rejected` |
| `applied_date` | Timestamp the request was submitted |
| `approved_by` | Foreign key → `admin.admin_id` |
| `approval_date` | Timestamp the request was decided |
| `admin_remarks` | Optional admin note |

### `leave_balance`
Per-employee, per-year leave allowance and remaining balance.

| Column | Notes |
|---|---|
| `emp_id` | Foreign key → `employees.emp_id` |
| `year` | Calendar year the balance applies to |
| `sick_leave` | Remaining sick leave days |
| `casual_leave` | Remaining casual leave days |
| `earned_leave` | Remaining earned leave days |

New employees are given 10 days of each leave type by default (see
`employee_api.php`).

## Entity relationships (summary)

```
departments 1───* employees *───1 shifts (via employee_shifts)
employees   1───* attendance
employees   1───* leave_applications *───1 admin (approved_by)
employees   1───1 leave_balance (per year)
```
