# Security Notes

This is a college/portfolio project. It was reviewed for common web application
security issues before publishing to GitHub. This document lists what was found,
what was fixed, and what is intentionally left as-is (with reasoning), so that
anyone reading the code understands its real security posture instead of assuming
it is production-ready.

**This application should not be deployed to a public, internet-facing server in
its current form.** It is suitable for local learning/demo use (e.g. via XAMPP on
your own machine).

## Fixed in this cleanup

- **Column-name SQL injection in `leave.php`** — the `apply_leave` action built a
  SQL column name directly from the `leave_type` POST value
  (`"{$leaveType}_leave"`). `mysqli_real_escape_string()` only protects *string
  literal* values, not identifiers like column names, so an attacker could submit
  an unexpected `leave_type` to manipulate the query. **Fix applied:** the value is
  now checked against a fixed whitelist (`sick`, `casual`, `earned`) before it is
  used to build the query.
- **Diagnostic scripts isolated** — `db_test.php`, `test.php`, and `test_system.php`
  print database status, table contents, and demo credentials with no login check.
  They have been moved out of the application root into `tools/`, given a loud
  warning banner, and documented in `tools/README.md`. **They should be deleted
  entirely before any real deployment.**

## Known issues, documented but not changed

These were left as-is because fixing them would change the application's login
flow, data model, or add new dependencies — which was out of scope for this
cleanup. They are listed here so they're not silently hidden.

### 1. Passwords hashed with MD5
`admin.password` and `employees.password` are stored as `MD5(password)`
(see `login.php`, `employee_api.php`). MD5 is a fast, broken hash — it is not
appropriate for passwords because it can be brute-forced or looked up in rainbow
tables very quickly.

**Recommended fix (not applied here):** migrate to PHP's built-in
[`password_hash()`](https://www.php.net/password_hash) /
[`password_verify()`](https://www.php.net/password_verify) functions
(bcrypt-based), which are the standard for PHP password storage. This requires:
- Changing the column to store the full hash string (`VARCHAR(255)`).
- Re-hashing all existing demo passwords.
- Replacing `MD5('$password')` comparisons in SQL with a `password_verify()` check
  in PHP after fetching the user by username/emp_code alone.

### 2. SQL queries built with string interpolation
Most queries in this project build SQL strings by directly interpolating
variables (e.g. `"... WHERE emp_id = $empId"`), rather than using parameterized
queries (`mysqli` prepared statements). String values are generally escaped with
`mysqli_real_escape_string()`, and numeric IDs are generally cast with `(int)`,
which reduces the risk — but this pattern is still more error-prone than
prepared statements, and one missed escape can introduce a real vulnerability.

**Recommended fix (not applied here):** migrate queries to `mysqli` prepared
statements (`$conn->prepare(...)` with bound parameters), which removes this
entire class of bug regardless of whether individual values are escaped.

### 3. Hardcoded default database credentials
`config/config.php` uses the default XAMPP MySQL credentials (`root`, no
password). This is normal for local development but must never be used on a
public server. See the note left directly in `config/config.php`.

### 4. No CSRF protection
Forms and AJAX actions (login, check-in/out, leave requests, employee
registration, etc.) do not use CSRF tokens. On a local single-user demo this is
low risk, but it means a malicious page could trigger state-changing requests
(like approving leave) if an admin session is active in the same browser.

### 5. Basic session security only
Sessions are started with PHP defaults — no session ID regeneration on login, no
explicit `session.cookie_httponly` / `session.cookie_secure` configuration. For a
real deployment, sessions should be hardened (regenerate session ID after login,
set secure cookie flags, add a session timeout).

### 6. Limited server-side input validation
Some validation (e.g. required fields, phone number pattern) is enforced only in
the browser (HTML `required`/`pattern` attributes), not re-checked on the server.
A request sent directly to the PHP endpoints (bypassing the HTML form) could skip
these checks. Numeric IDs are cast with `(int)` in most places, which helps, but
field content (e.g. email format) is not strictly re-validated server-side.

### 7. Errors are suppressed globally
`config/config.php` sets `error_reporting(0)` and `display_errors = 0`. This is
reasonable for hiding errors from end users (especially since several endpoints
return JSON), but it also means PHP errors/warnings during development are
silently swallowed. Consider logging errors to a file (`error_log`) instead of
fully disabling them, especially while developing.

## Reporting

This is a student project without a formal security disclosure process. If you
fork this project and find an issue, feel free to open a GitHub issue describing
it.
