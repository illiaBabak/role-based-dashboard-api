# 🔐 Role-Based Dashboard API

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)

**Backend pet project for learning role-based access control, DTO pattern, and Docker through a PHP REST API.**

</div>

---

## 📖 About

**Goal of the project** – to practice and learn:

- **Role-based access control** – `admin` / `user` roles with different permissions
- **DTO pattern** – separating input data from persistence layer
- **Docker** – containerized PHP + MySQL + phpMyAdmin setup
- **Cookie-based auth** – sessions with token generation and expiry

The API provides user management with registration, login, and CRUD operations where access is restricted based on user roles.

---

## ✨ Main Features

- **Two roles**: `admin` and `user` with different access levels
  - 🛡️ **Admin** – can view, update, and delete all users
  - 👤 **User** – can only view and manage themselves
- **Cookie-based authentication** with secure httpOnly tokens
- **DTO layer** – `InputUserDTO` (registration) and `PersistUserDTO` (persistence) for clean data flow
- **Password hashing** with `password_hash` / `password_verify`
- **CORS** configured for frontend on `localhost:3000`
- **Docker Compose** – one command to spin up API + MySQL + phpMyAdmin

---

## 🛠 Tech Stack

- **Language**: PHP 8 (`strict_types`)
- **Database**: MySQL 8 (via `mysqli`)
- **Autoloading**: Composer PSR-4
- **Containerization**: Docker + Docker Compose
- **DB Admin**: phpMyAdmin

---

## 🚀 Getting Started

1. **Clone the repository**

```bash
git clone https://github.com/your-username/role-based-dashboard-api.git
cd role-based-dashboard-api
```

2. **Create `.env`**

```env
DB_HOST=db
DB_USER=your_mysql_user
DB_PASSWORD=your_mysql_password
DB_NAME=rbd
DB_ROOT_PASSWORD=your_root_password
```

3. **Run with Docker**

```bash
docker compose up --build
```

This will start three services:

| Service    | URL                   | Purpose        |
| ---------- | --------------------- | -------------- |
| API        | http://localhost:8000 | PHP REST API   |
| MySQL      | localhost:3306        | Database       |
| phpMyAdmin | http://localhost:8080 | DB admin panel |

The database schema (`users` + `user_sessions` tables) is initialized automatically via `mysql/init/rbd.sql`.

---

## 📡 API Overview

### Auth

| Method | Endpoint         | Description                      |
| ------ | ---------------- | -------------------------------- |
| POST   | `/auth/register` | Register a new user              |
| POST   | `/auth/login`    | Login and receive session cookie |
| POST   | `/auth/logout`   | Logout and destroy session       |
| GET    | `/auth/me`       | Get current authenticated user   |

### Users

| Method | Endpoint      | Description                              |
| ------ | ------------- | ---------------------------------------- |
| GET    | `/users`      | List users (admin: all, user: self only) |
| PATCH  | `/users/{id}` | Update user name/role                    |
| DELETE | `/users/{id}` | Delete user                              |

### Response Format

```json
// Success
{ "data": { ... }, "error": null }

// Error
{ "data": null, "error": "Error message" }
```

---

## 🗄 Database Schema

**`users`**

| Column          | Type                    | Description       |
| --------------- | ----------------------- | ----------------- |
| `id`            | `int UNSIGNED` PK       | Auto-increment ID |
| `login`         | `text`                  | User login        |
| `hash_password` | `text`                  | Bcrypt hash       |
| `name`          | `text`                  | Display name      |
| `role`          | `enum('admin', 'user')` | User role         |

**`user_sessions`**

| Column       | Type              | Description           |
| ------------ | ----------------- | --------------------- |
| `id`         | `int UNSIGNED` PK | Auto-increment ID     |
| `user_id`    | `int UNSIGNED` FK | References `users.id` |
| `token`      | `varchar(255)`    | Unique session token  |
| `expires_at` | `datetime`        | Token expiry (7 days) |
| `created_at` | `datetime`        | Creation timestamp    |

---

## 📁 Project Structure

```text
public/
  index.php               // Entry point, CORS, routing
app/
  Controllers/
    AuthController.php     // Register, login, logout, me
    UsersController.php    // Get, update, delete users
  Services/
    AuthService.php        // Auth logic, token generation, sessions
    UsersService.php       // Role-based user management
  Models/
    UsersModel.php         // Users table queries
    SessionsModel.php      // Sessions table queries
  DTO/
    InputUserDTO.php       // Registration input (login, password, name, role)
    PersistUserDTO.php     // Persistence input (login, name, role)
core/
  Router.php               // Custom router with dynamic {id} params
  Response.php             // JSON response helper
  MySQLConnect.php         // Database connection
mysql/init/
  rbd.sql                  // DB schema init script
  rbd_test.sql             // Test DB schema
compose.yaml               // Docker Compose (api + db + phpmyadmin)
compose.test.yaml           // Docker Compose for test environment
Dockerfile                  // PHP container with mysqli
```

---
