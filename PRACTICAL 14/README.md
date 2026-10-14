# Practical 14 — Admin Dashboard with RBAC, Dynamic Menu, Audit Log

## Run (XAMPP, Windows)
1. Start **Apache + MySQL** in XAMPP.
2. Open `http://localhost/PRACTICAL%2014/setup.php` once → creates `practical14_db` + seeds data.
3. Login: `http://localhost/PRACTICAL%2014/auth/login.php`
   - `admin@example.com / admin123` (full access)
   - `staff@example.com / staff123` (dashboard ❌, students/events/registrations ✔)
   - `student@example.com / student123` (only My Profile)

## Key-question mapping (for viva)
| Q | Where |
|---|---|
| 1. Dashboard values dynamic? | `admin/dashboard.php` — `COUNT(*)` on users/students/events/registrations + latest-registrations JOIN + monthly `GROUP BY` chart |
| 2. Restricted to admin? | `includes/auth.php` `require_role(['admin'])` on dashboard, users, audit_logs; staff redirected to `access_denied.php` (verified 302) |
| 3. Role-based menus? | `menus` table + `includes/menu.php::render_menu()` filters by `allowed_roles` CSV |
| 4. Audit log? | `includes/audit.php::audit_log()` on LOGIN/LOGOUT/USER_STATUS_TOGGLE/STUDENT_*/EVENT_*/REGISTRATION_*; viewed in `admin/audit_logs.php` |

## Extensions done
- **Intermediate:** active/inactive toggle in `admin/users.php` (self-toggle blocked; inactive user forced logout on next request).
- **Advanced:** Chart.js bar chart of registrations by month in `admin/dashboard.php`.

## Audit-log test cases (post-lab)
1. Login as admin → row `LOGIN` appears in `audit_logs`.
2. Toggle a user inactive → row `USER_STATUS_TOGGLE` appears; that user can no longer log in.
3. Add/delete student or event → `STUDENT_CREATE / EVENT_DELETE` rows appear with IP + timestamp.
4. Staff opening `/admin/dashboard.php` → 302 to `access_denied.php` (RBAC correctness).
5. Staff opening `/admin/users.php` or `/admin/audit_logs.php` → denied; `/admin/students.php` → allowed (dynamic menu shows only allowed items).
