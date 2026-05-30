# User Flow: Login to Dashboard to Settings

This document explains the user journey in the Nachias ERP web application from the login screen through the dashboard and into the settings module. It is based on the current Laravel routes, controllers, middleware, and Blade views in this project.

## Scope

- Login page and authentication flow
- Protected dashboard landing page
- Top navigation and access to settings
- Settings view, edit, validation, save, and feedback flow
- Related access-control and error paths

## Key Files

| Area | File |
| --- | --- |
| Web routes | `routes/web.php` |
| Login view | `resources/views/login.blade.php` |
| Authentication controller | `app/Http/Controllers/AuthController.php` |
| Auth middleware | `app/Http/Middleware/CheckAdminLogin.php` |
| Role-active middleware | `app/Http/Middleware/CheckRoleActive.php` |
| Employee-active middleware | `app/Http/Middleware/CheckEmployeeActive.php` |
| Dashboard controller | `app/Http/Controllers/HomeController.php` |
| Dashboard view | `resources/views/dashboard.blade.php` |
| Shared layout | `resources/views/layouts/common.blade.php` |
| Top navigation | `resources/views/layouts/topbar.blade.php` |
| Settings controller | `app/Http/Controllers/SettingController.php` |
| Settings view | `resources/views/settings.blade.php` |

## Route Summary

| User Action | HTTP Method | URL | Controller / View |
| --- | --- | --- | --- |
| Open login page | `GET` | `/` | Returns `login` view |
| Submit login | `POST` | `/login` | `AuthController@authentication` |
| Open dashboard | `GET` / `POST` | `/dashboard` | `HomeController@index` |
| Open settings | `GET` | `/settings` | `SettingController@index` |
| Save settings | `POST` | `/settings/update` | `SettingController@update` |
| Logout | `GET` / `POST` | `/logout` | `AuthController@logout` |

The dashboard, profile, logout, settings, and other ERP module routes are inside this protected middleware group:

```php
Route::middleware(['auth.admin', 'auth.session', 'role.active', 'employee.active'])->group(function () {
    // protected routes
});
```

## Main User Flow

### 1. User Opens the Application

1. User visits the application root URL `/`.
2. Laravel returns `resources/views/login.blade.php`.
3. The login screen displays:
   - Nachias branding
   - Email input
   - Password input
   - Remember Me checkbox
   - Login button
   - Flash validation or error messages, when available

### 2. User Enters Login Credentials

1. User enters an email address and password.
2. User can optionally select Remember Me.
3. User clicks Login.
4. The form posts to `/login` with CSRF protection.

### 3. System Validates Login Input

`AuthController@authentication` validates:

| Field | Rule |
| --- | --- |
| Email | Required, valid email format |
| Password | Required, 8 to 15 characters, must contain uppercase, lowercase, number, and special character |

If validation fails:

1. User is redirected back to `/`.
2. Validation messages are shown on the login form.
3. Previously entered email is preserved through old input.

### 4. System Authenticates the User

After validation passes:

1. The controller builds credentials from `email` and `password`.
2. Laravel attempts login with `Auth::attempt($credentials, $remember)`.
3. If Remember Me is selected:
   - Email, password, and remember flag are queued into cookies.
4. If Remember Me is not selected:
   - Existing remember cookies are cleared.

If credentials are invalid:

1. User is redirected to `/`.
2. A danger flash message is shown: `Enter a valid credentials.`

If credentials are valid:

1. User session is authenticated.
2. User is redirected to the intended protected page, defaulting to `/dashboard`.

## Protected Access Checks

Before the user can access `/dashboard` or `/settings`, the request passes through the protected middleware group.

### Authentication Check

`CheckAdminLogin` confirms the user is logged in using the web guard.

If not logged in:

1. User is redirected to `/`.
2. A flash message asks the user to log in.

### Session Authentication

Laravel's `auth.session` middleware validates the authenticated session.

### Role Status Check

`CheckRoleActive` verifies the user's role is active.

If the user is not Super Admin and the role is inactive:

1. User is logged out.
2. User is redirected to `/`.
3. A flash message says the role has been deactivated.

### Employee Status Check

`CheckEmployeeActive` verifies the user's account status.

If the user is not Super Admin and their status is not `Active`:

1. User is logged out.
2. Session is invalidated.
3. CSRF token is regenerated.
4. User is redirected to `/`.
5. A flash message says the account has been deactivated.

## Dashboard Flow

### 1. Dashboard Loads After Login

After successful login, the user reaches `/dashboard`.

`HomeController@index` collects dashboard data for:

- Sales and orders
- Employee attendance
- Accounts and financial values
- Debtors and creditors aging
- Stock values
- Work in progress
- Production metrics
- Maintenance requirements
- Expiring documents
- Monthly chart data

The controller returns `resources/views/dashboard.blade.php` with the collected data.

### 2. Dashboard Displays Based on Permissions

The dashboard view contains multiple sections. Each section is conditionally visible based on either:

- User ID `1`, treated as Super Admin
- Specific dashboard permissions

Main dashboard sections include:

| Section | Permission Pattern |
| --- | --- |
| Sales & Order Dashboard | `view-sales-order dashboard` |
| Employee Attendance Dashboard | `view-attendance dashboard` |
| Accounts & Financial Dashboard | `view-accounts-financial dashboard` |
| Production Dashboard | `view-production dashboard` |
| Maintenance | `view-maintenance dashboard` |

If a user lacks a section permission, that section is not rendered in the dashboard view.

### 3. User Uses Top Navigation

All authenticated pages use:

```blade
@include('layouts.header')
@include('layouts.topbar')
@yield('content')
@include('layouts.footer')
```

The top navigation includes:

- Nachias brand link back to `/dashboard`
- Main module menu
- User avatar dropdown
- My Profile link
- Logout button

The Settings menu item appears only when:

- The user is Super Admin, or
- The user has the `view settings` permission

## Settings Module Flow

### 1. User Opens Settings

1. User clicks Settings in the top navigation.
2. Browser opens `/settings`.
3. Request passes through the same protected middleware group.
4. `SettingController@index` runs.

### 2. System Loads Settings Form Data

`SettingController@index` loads:

- The first `Setting` record, with related state and city
- Active states
- Active cities for the selected state, if a state is already selected

The controller returns `resources/views/settings.blade.php`.

### 3. User Reviews or Edits Company Settings

The settings page contains these major groups:

| Group | Fields |
| --- | --- |
| Company details | Company Name, Email, Logo, QR Code, Phone Number, Toll Free No, State, City, Address, Zip Code |
| Prefix settings | PO Prefix, SO Prefix |
| Tax info | CGST, SGST, IGST, PAN, GST, CIN |
| Bank details | Bank Name, Branch Location, Account No, IFSC Code |
| Working days and time | Working Days Range, Opening Time, Closing Time |
| Other | Terms and Conditions |

The Submit button is shown only when:

- The user is Super Admin, or
- The user has the `edit settings` permission

This means a user with only `view settings` can open the settings page but cannot submit edits from the UI.

### 4. User Selects Working Days and Time

The working-days control uses day pills for Monday through Sunday.

User interaction:

1. First click selects the start day.
2. Second click selects the end day.
3. The hidden `working_days` input is updated.
4. If both days are selected, the value is stored as a range like `Monday - Saturday`.

Opening and closing time fields use Flatpickr time pickers.

### 5. User Saves Settings

1. User clicks Submit.
2. The form posts to `/settings/update`.
3. The form uses `multipart/form-data` to support logo and QR code uploads.
4. `SettingController@update` validates all submitted data.

Important validation rules include:

| Field | Rule |
| --- | --- |
| Company Name | Required, 3 to 100 characters |
| Email | Required string, up to 128 characters |
| Logo | Optional image, jpeg/jpg/png/gif/svg, up to 1024 KB |
| QR Code | Optional image, jpeg/jpg/png/gif/svg, up to 1024 KB |
| Phone Number | Required, up to 15 characters, phone-format regex |
| State | Required, must exist in `states` |
| City | Required, must exist in `cities` |
| Address | Required, up to 1000 characters |
| Zip Code | Required, exactly 6 characters |
| CGST / SGST / IGST | Required integer, 0 to 100 |
| PAN | Optional, PAN-format regex |
| GST | Optional, GST-format regex |
| CIN | Optional, CIN-format regex |
| Terms and Conditions | Required |

### 6. System Handles Uploaded Files

If a new logo is uploaded:

1. File is moved to `public/uploads/logo`.
2. Existing logo file is deleted if present.
3. New filename is stored in settings data.

If a new QR code is uploaded:

1. File is moved to `public/uploads/qr_code`.
2. Existing QR code file is deleted if present.
3. New filename is stored in settings data.

### 7. System Creates or Updates Settings

If a settings record already exists:

1. Old data is captured.
2. Existing record is updated.
3. New data is captured.
4. An update log is created with `addLog`.
5. Success message: `Settings updated successfully`.

If no settings record exists:

1. A new settings record is created.
2. A create log is created with `addLog`.
3. Success message: `Settings created successfully`.

Finally, the user is redirected back to `/settings`.

## Error and Alternate Flows

### Invalid Login Input

User stays on the login flow and sees field-level validation messages.

### Invalid Login Credentials

User is redirected to `/` and sees a danger flash message.

### User Not Logged In

Direct access to `/dashboard` or `/settings` redirects back to `/`.

### Inactive Role

User is logged out and redirected to `/` with a role deactivation message.

### Inactive Employee Account

User is logged out, session is invalidated, and the user is redirected to `/` with an account deactivation message.

### Missing Settings View Permission

The Settings menu item is not rendered in the top navigation. If the user manually opens the URL, route-level permission middleware is not currently defined for `/settings`; access relies on the authenticated route group and UI-level menu visibility.

### Missing Settings Edit Permission

The Settings page can be viewed, but the Submit button is hidden unless the user is Super Admin or has `edit settings`.

### Settings Validation Failure

The user is redirected back to `/settings`, field-level validation errors are displayed, and old input values are preserved.

## End-to-End Happy Path

1. User opens `/`.
2. User enters valid email and password.
3. User clicks Login.
4. System validates input.
5. System authenticates credentials.
6. User is redirected to `/dashboard`.
7. Dashboard data is calculated and rendered according to permissions.
8. User clicks Settings in the top navigation.
9. System loads existing company settings, active states, and active cities.
10. User updates company, tax, bank, working time, or terms fields.
11. User clicks Submit.
12. System validates the form.
13. System saves uploaded files, if provided.
14. System updates or creates the settings record.
15. System writes an audit log.
16. User returns to `/settings` with a success message.

## Notes for QA

- Verify login validation messages for blank email, invalid email, weak password, and invalid credentials.
- Verify Remember Me stores and clears expected cookies.
- Verify `/dashboard` and `/settings` redirect to `/` when unauthenticated.
- Verify inactive role and inactive employee behavior using non-Super Admin users.
- Verify Settings menu visibility for Super Admin, `view settings`, and users without settings permission.
- Verify Submit button visibility for Super Admin, `edit settings`, and view-only users.
- Verify settings save with and without logo or QR code uploads.
- Verify state and city dropdown values load correctly from active records.
- Verify settings changes create audit log records through `addLog`.
