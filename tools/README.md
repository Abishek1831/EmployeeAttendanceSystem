# Developer Diagnostic Tools (Not Part of the Application)

This folder contains three diagnostic scripts that were used during development to
verify that the database was set up correctly:

- `db_test.php`
- `test.php`
- `test_system.php`

## ⚠️ Security Warning

These scripts are **not part of the Employee Attendance Management System** itself.
They exist only to help a developer confirm a local XAMPP/MySQL setup is working.
They:

- Print database connection status, table names, and row counts directly to the page.
- Display the **demo admin and employee credentials** in plain text.
- Have **no login check**, so anyone who can reach the URL can view this information.

**Do not deploy this folder to a public server.** They are kept here (instead of the
project root) so they are not served alongside the real application, and are excluded
from typical web roots as described in `docs/SETUP.md`. For any real deployment,
delete this entire `tools/` folder.

## When to use them

Only run these locally, after importing your database, to confirm:

- MySQL is reachable
- The `employee_attendance` database and its tables exist
- The demo admin/employee accounts were inserted correctly

Then browse to `tools/test_system.php` (or `test.php` / `db_test.php`) directly,
e.g. `http://localhost/employee-attendance-system/tools/test_system.php`.
