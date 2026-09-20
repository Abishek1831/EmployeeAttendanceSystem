# Features

A detailed breakdown of what the Employee Attendance Management System does,
based on the actual implemented functionality.

## Authentication

- Single login page (`index.php`) with a toggle between **Admin Login** and
  **Employee Login**.
- Admin logs in with a username/password; employees log in with their employee
  code (e.g. `EMP001`) and password.
- Login is handled asynchronously via `login.php`, which returns JSON and the
  page redirects on success.
- Passwords are checked against `MD5()` hashes stored in the database (see
  [SECURITY.md](../SECURITY.md) for why this should be upgraded).
- `logout.php` destroys the session and redirects to the login page.
- Both dashboards redirect back to `index.php` if accessed without a valid
  session of the right type.

## Admin features (`admin_dashboard.php`)

- **Dashboard overview**: total active employees, number present today, number
  on leave, and number absent — computed live from `employees` and `attendance`.
- **Today's attendance list**: every active employee with their check-in/out
  time and status for the current day.
- **Pending leave requests**: a queue of leave applications awaiting a decision.
- **Approve/reject leave**: admins can approve or reject a pending leave
  request (`leave.php` → `approve_leave` action). Approving a request
  automatically deducts the requested days from the employee's leave balance
  for that leave type.
- **Register new employee**: link to `register_employee.php`, a form for
  adding a new employee (see below).

## Employee registration (`register_employee.php`, admin-only)

- Admin-only form (redirects to login if a non-admin tries to access it).
- Collects employee code, department, name, email, phone, password, joining
  date, shift, and status.
- On submit (`employee_api.php` → `register_employee` action):
  - Rejects duplicate employee codes and duplicate emails.
  - Inserts the new employee with an `MD5()`-hashed password.
  - Assigns the selected shift (`employee_shifts` table).
  - Initializes a leave balance of 10 sick, 10 casual, and 10 earned leave
    days for the current year.

## Employee features (`employee_dashboard.php`)

- **Profile summary**: employee name and department.
- **Check-in / Check-out**: one-click attendance actions
  (`attendance.php` → `check_in` / `check_out`).
  - Check-in compares the current time against the employee's assigned shift
    start time plus a grace period; if the employee checks in after that
    window, the day is marked `late` instead of `present`.
  - Check-out records the time and calculates worked hours.
  - The system prevents checking in twice in one day, and checking out without
    an active check-in.
- **Monthly attendance stats**: present days, absent days, late days, and
  current total leave balance for the month.

## Leave management (`employee_leave.php`)

- **Leave balance display**: remaining sick, casual, and earned leave for the
  current year.
- **Apply for leave**: employees choose a leave type, start date, end date,
  and reason. The number of days is calculated automatically from the date
  range, and the system blocks the request if the employee doesn't have
  enough balance left for that leave type.
- **Leave history**: a full list of the employee's past and pending leave
  requests, sorted with pending requests first, then most recent.

## Department and shift management

- Departments and shifts are set up directly in the database (see
  [`database/README.md`](../database/README.md) for the schema) and are used
  throughout the app:
  - The registration form pulls the live list of departments and shifts for
    its dropdowns.
  - Each employee's department is shown on their dashboard and in the admin's
    attendance list.
  - Each employee's assigned shift determines what counts as "on time" vs.
    "late" for check-in.

## Developer diagnostic tools (`tools/`)

Three standalone scripts used during development to sanity-check the database
setup (connection, tables, demo accounts, sample data). Not part of the
application's normal flow — see [`tools/README.md`](../tools/README.md) for
important warnings before using them.
