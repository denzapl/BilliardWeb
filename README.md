# Billiard Clubs Project - Milestone 1 & 2 Scaffolding

This repository contains a ready-to-use project scaffold for a PHP (FlightPHP) + MySQL backend and a static frontend single-page application (SPA) using HTML/CSS/JS and Bootstrap.

## What is included
- Frontend (SPA) with separate view files:
  - `frontend/views/home.html`
  - `frontend/views/inner.html`
  - `frontend/views/create.html`
  - `frontend/views/update.html`
  - `frontend/views/profile.html`
  - `frontend/index.html` loads common CSS/JS once and uses AJAX to load views (hash routing).
  - `frontend/css/common.css`
  - `frontend/js/common.js`

- Backend (PHP/REST using FlightPHP):
  - `backend/index.php` (entry point using Flight routes)
  - `backend/routes/` (route definitions)
  - `backend/services/` (business logic / auth)
  - `backend/dao/` (DAO classes using PDO for Users and BilliardClubs plus 3 example entities)
  - `backend/config.php` (DB config)
  - `backend/composer.json` (for FlightPHP and firebase/php-jwt)

- Migrations / SQL:
  - `migrations/create_tables.sql->run script inside MYSQL Workbench
`
- OpenAPI (basic)
  - `openapi.yaml`

## Setup (XAMPP + MySQL Workbench)
1. Place `backend` folder into your XAMPP `htdocs` directory (e.g. `C:/xampp/htdocs/billiard_project/backend`).
2. Ensure PHP and Apache are running in XAMPP.
3. Install composer dependencies inside the backend folder:
   ```
   cd C:/xampp/htdocs/billiard_project/backend
   composer install
   ```
   (Composer is required to fetch FlightPHP and firebase/php-jwt.)
4. Create a MySQL database and import the SQL files located in `migrations/`.
   Use MySQL Workbench or phpMyAdmin.
5. Update `backend/config.php` with your DB connection credentials.
6. Access the frontend by opening `frontend/index.html` in a browser (served via file:// or via simple HTTP server).
   For API endpoints, point AJAX to `http://localhost/billiard_project/backend/index.php` (adjust path as needed).

- JWT authentication and PDO usage are scaffolded — you will need to set `JWT_SECRET` in `backend/services/AuthService.php`.

