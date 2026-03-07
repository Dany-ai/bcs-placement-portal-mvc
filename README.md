# BCS Placement Portal (PHP MVC)

A role-based placement management portal built with a custom PHP MVC architecture.  
The system allows students to manage profiles and upload CVs, employers to create and manage placement opportunities, and admins to review placements and provide career support through internal messaging.

---

## Overview

This project was originally developed as a university hackcamp project and later refactored into a cleaner, more secure, and more portfolio-ready codebase.

It demonstrates:

- custom MVC architecture in PHP
- role-based authentication and access control
- secure session handling
- CSRF protection on state-changing actions
- secure PDF CV upload outside the public web root
- placement approval workflow
- student-to-placement matching
- internal messaging between users
- SQLite-based local setup with migrations and demo seed data

---

## Features

### Student
- Register and log in
- Edit profile and skills
- Upload CV as PDF
- View recommended placement matches
- Apply for placements
- Chat with career support/admin
- Read inbox messages

### Employer
- Register and log in
- Manage organisation profile
- Create placement opportunities
- Edit and delete placements
- View applicants for placements
- Receive admin approval/rejection messages

### Admin / Career Support
- Review pending placements
- Approve or reject employer submissions
- Search students
- Chat directly with students
- Send system and support messages

---

## Security Improvements

This version includes several improvements over the original coursework-style build:

- `password_hash()` / `password_verify()` for modern password storage
- legacy SHA-256 login migration support for older seeded accounts
- CSRF protection on POST-based actions
- session hardening with secure defaults
- CV upload validation using:
  - MIME type checking
  - PDF file signature validation
  - file size limits
- uploads stored outside the public directory
- storage access blocked with `.htaccess`
- role-based route protection

---

## Tech Stack

- PHP 8+
- SQLite
- PDO
- HTML
- CSS
- JavaScript
- Apache with `mod_rewrite`

---

## Project Structure

```text
bcs-placement-portal-mvc
├─ app
│  ├─ controllers
│  ├─ core
│  ├─ models
│  └─ views
├─ config
├─ database
│  └─ migrations
├─ public
│  └─ assets
├─ scripts
└─ storage