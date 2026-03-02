# BCS Placement Portal (PHP MVC)

A role-based placement management portal built using a custom PHP MVC architecture.

## Features

- Role-based authentication (Student / Employer / Admin)
- Secure password hashing (password_hash with legacy migration support)
- Placement publishing and approval workflow
- Student–placement matching engine
- Internal messaging system
- Secure CV upload (PDF-only, MIME-validated)
- SQLite database with migrations

---

## Tech Stack

- PHP (Custom MVC)
- SQLite
- PDO
- HTML/CSS/JavaScript
- Apache (mod_rewrite)

---

## Setup (Local)

### Requirements
- PHP 8+
- Apache with `mod_rewrite` (XAMPP/Laragon ok)

### 1) Clone
```bash
git clone https://github.com/Dany-ai/bcs-placement-portal-mvc.git
cd bcs-placement-portal-mvc