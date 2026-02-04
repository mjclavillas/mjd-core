# 📦 mjd-core

### *High-Performance PHP Framework with Dependency Injection*

![PHP](https://img.shields.io/badge/PHP-%3E%3D_8.1-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)
![Build](https://img.shields.io/badge/Build-v1.0--Stable-indigo?style=for-the-badge)

---

## 📖 Overview
**mjd-core** is a minimalist, industrial-grade PHP framework designed for developers who value transparency, speed, and clean code. Built with a custom **Dependency Injection** container and a reflection-based **Routing Engine**, it eliminates framework bloat while providing the essential tools for modern web applications.



---

## ✨ Key Features
* ⚡ **Reflection-Based DI:** Automatic "Auto-Wiring" of controller dependencies.
* 🛣️ **Grouped Routing:** Support for route prefixes, middleware pipelines, and API grouping.
* 🏗️ **Active Record ORM:** Fluent database interactions and schema management.
* 🛠️ **MJDC Binary:** A dedicated CLI assistant for scaffolding (Controllers, Models, Migrations).
* 🎨 **Twig Integration:** Native support for Twig templates with custom session/auth extensions.
* 💻 **Interactive Console:** Built-in API testing suite directly in the dashboard.

---

## 🏗️ Core Architecture
The framework follows a strict **Request-Response** pipeline:
1. **Entry Point:** All traffic is funneled through `public/index.php`.
2. **Bootstrapping:** Environment variables and Sessions are initialized.
3. **Routing:** The `Router` resolves the URI and injects dependencies into the target Controller.
4. **Middleware:** Requests pass through security layers (Auth, CSRF, etc.).
5. **Output:** The Controller returns a `View` (HTML) or a `JSON` stream.



---

## 📂 Directory Structure
```text
├── app/                # Application Logic
│   ├── Controllers/    # Request Handlers
│   ├── Models/         # Database Entities
│   └── Middleware/     # Security Layers
├── bootstrap/          # Framework Boot Logic
├── public/             # Web Root (index.php, Assets)
├── routes/             # Web & API Route Definitions
├── src/                # MJD-Core Engine (Framework Source)
├── storage/            # Logs, Cache, and File Uploads
└── views/              # Twig Templates
```

---

## 🛠️ Installation

### 1. Clone & Install
```bash
git clone [https://github.com/your-username/mjd-core.git](https://github.com/your-username/mjd-core.git)
cd mjd-core
composer install
```

### 2. Environment Setup
```bash
cp .env.example .env
```
# Update DB_DATABASE and APP_URL in your .env file

### 3. Database & Scaffolding
```bash
php mjdc migrate
php mjdc db:seed
```

### 4. Launch Server
```bash
php mjdc serve
```
---

## ⌨️ CLI Assistant (mjdc)

| Command | Description |
| :--- | :--- |
| `serve [host] [port]` | Boots the PHP development server : Default localhost 8000 |
| `make:controller` | Generates a new Controller in app/Controllers |
| `make:model` | Creates a Model and a Migration file |
| `migrate` | Executes pending migrations |
| `db:seed` | Populates tables with test data |

---

## 📄 License
This project is open-sourced software licensed under the **MIT License**.