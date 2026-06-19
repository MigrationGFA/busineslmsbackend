## Remsana Backend Core (PHP / CodeIgniter 4)

This folder is a **lean skeleton** for the authoritative backend core, implemented with **CodeIgniter 4 + MySQL 8**, aligned to the execution architecture:

- PHP (CodeIgniter 4) – authoritative core
- Node – behavioural / engagement services
- MySQL 8 – canonical state + analytics

> IMPORTANT: This skeleton does **not** include the CodeIgniter 4 framework itself.  
> Your team must run Composer to install CI4 and vendors.

---

### 1. Getting started

From the project root:

```bash
cd backend-php

# If starting from scratch (recommended)
composer create-project codeigniter4/appstarter .

# Or, if composer.json is already pointing to CI4, just install:
composer install
```

Then copy or merge the contents of the `app/` and `app/Config/Routes.php` stubs in this folder into your CI4 app.

Configure your `.env` (or `app/Config/Database.php`) to point to **MySQL 8**:

```env
database.default.hostname = localhost
database.default.database = remsana_core
database.default.username = remsana_user
database.default.password = your_password
database.default.DBDriver = MySQLi
```

---

### 2. What this backend owns

This CodeIgniter backend is the **authoritative core** for:

- Identity & Tenancy (`users`, `admin_users`)
- Subscription & Entitlements (`subscriptions`)
- Finance & Bookkeeping (payments/transactions – to be extended)
- Learning & Capability (100‑day programme, lessons, quizzes, progress, certificates)
- Audit logs (`audit_logs`)
- Analytics staging (`analytics.events`, `analytics.learning_events`, daily aggregates)

It exposes REST APIs for:

- `/api/v1/auth/*` – regular user auth
- `/api/v1/learning/*` – curriculum & progress
- `/api/insider/auth/*` – admin/analyst auth
- `/api/insider/admin/*` and `/api/insider/analyst/*` – dashboards
- `/api/v1/xapi/*` – optional xAPI statement ingestion

---

### 3. Migrations & schema

We recommend modelling your schema in **MySQL Workbench** and keeping it in sync via CI4 migrations.

- Use the MySQL DDL already defined in the architecture docs for:
  - `users`, `subscriptions`, `admin_users`, `audit_logs`
  - `learning_programmes`, `learning_modules`, `learning_lessons`, `learning_resources`
  - `learning_quizzes`, `learning_quiz_questions`, `learning_quiz_options`, `learning_quiz_attempts`
  - `learning_lesson_progress`, `learning_certificates`, `learning_xapi_statements`
  - `analytics.events`, `analytics.learning_events`, `daily_revenue`, `daily_cac_registrations`, `daily_churn`, cohorts

Run migrations with:

```bash
php spark migrate
```

---

### 4. API surface (high‑level)

**Auth (SME users)**

- `POST /api/v1/auth/login`
- `POST /api/v1/auth/refresh`
- `POST /api/v1/auth/logout`

**Auth (Insider – Admin / Analyst)**

- `POST /api/insider/auth/login`
- `POST /api/insider/auth/refresh`
- `POST /api/insider/auth/logout`

**Learning / 100‑Day Programme**

- `GET /api/v1/learning/programmes/{code}` – e.g. `100DAY_SME`
- `GET /api/v1/learning/lessons/{lessonId}`
- `GET /api/v1/learning/progress/me`
- `POST /api/v1/learning/lessons/{lessonId}/view`
- `POST /api/v1/learning/lessons/{lessonId}/video-progress`
- `POST /api/v1/learning/quizzes/{quizId}/attempt`
- `POST /api/v1/learning/certificates` (issue certificate when criteria are met)

**Admin / Analyst Dashboards**

- `/api/insider/admin/*` – users, finances, CAC, content, system health, audit logs
- `/api/insider/analyst/*` – KPIs, cohorts, revenue analytics, registrations, learning metrics

---

### 5. How web & mobile use this backend

- **Web (React, this repo)**:
  - Configure `VITE_API_BASE_URL=http://localhost:8080/api/v1` (or your CI4 host).
  - Replace mock/localStorage flows with calls to the endpoints above.

- **Mobile (Expo)**:
  - Set `EXPO_PUBLIC_API_URL=http://localhost:8080/api/v1`.
  - Use the existing `mobile/src/api/client.ts` (axios) to talk to this backend.

---

### 6. Next steps for your team

1. Run Composer to pull in CodeIgniter 4.
2. Implement the migrations from the agreed MySQL schema.
3. Flesh out the controllers (Auth, Learning, Insider Admin/Analyst) following this skeleton.
4. Point the React and Expo apps at this backend and incrementally move logic from the frontend into PHP.



# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
