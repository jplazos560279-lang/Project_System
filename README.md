# HRMS - Human Resource Management System

A Laravel-based Human Resource Management System for managing employees, departments, positions, attendance, and payroll.

## Admin Account

| Field    | Value             |
|----------|-------------------|
| Name     | Admin             |
| Email    | admin@email.com   |
| Password | password          |
| Role     | admin             |

## Features

- **Dashboard** – Overview of total employees, today's attendance, pending payroll, and departments
- **Employees** – CRUD management with department and position associations
- **Departments** – CRUD management
- **Positions** – CRUD management with salary info
- **Attendance** – Daily time-in/time-out recording
- **Payroll** – Salary, deductions, and net salary tracking

## Setup

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure your database in .env, then run migrations
php artisan migrate:fresh --seed

# Build assets
npm run build

# Serve
php artisan serve
```

## Default Seeded Data

Running `php artisan migrate:fresh --seed` creates:
- 1 admin user (see credentials above)
- Some demo departments, positions, employees, attendances, and payrolls

## Tech Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Tailwind CSS
- Vite

## License

[MIT](https://opensource.org/licenses/MIT)
