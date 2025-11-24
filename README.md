# 🍽️ Lancaster's Restaurant Booking System

**A full-stack web application for managing restaurant reservations and staff scheduling.**

![Project Status](https://img.shields.io/badge/Status-Completed-success)
![Tech Stack](https://img.shields.io/badge/Tech-PHP%20%7C%20MySQL%20%7C%20Twig-blue)
![Type](https://img.shields.io/badge/Type-Academic%20Project-orange)

---

## 📍 Overview
**Lancaster's Booking System** is a robust web application designed to streamline table reservations. It features a dual-interface system: a customer-facing portal for booking tables and a secure staff dashboard for managing services, viewing reservations, and configuring table availability.

Built using **raw PHP** and the **Twig templating engine**, this project demonstrates a strong understanding of backend logic, database management, and secure authentication practices without relying on heavy frameworks.

---

## ✨ Key Features

### 👤 Customer Portal
* **Service Selection:** Browse upcoming Breakfast, Lunch, or Dinner services.
* **Table Reservation:** Book specific time slots based on real-time availability.
* **Account Management:** Sign up/Login to view booking history and manage details.

### 👨‍🍳 Staff Dashboard
* **Service Management:** Create and configure dining services (e.g., set max tables, times).
* **Reservation Tracking:** View a comprehensive list of all bookings per service.
* **Dynamic Controls:** Add services for specific dates via an intuitive date-picker interface.
* **Secure Authentication:** First-run staff account setup and protected admin routes.

---

## 🛠️ Tech Stack

* **Backend:** PHP (v8.0+)
* **Database:** MySQL (Relational DB)
* **Templating Engine:** Twig (via Composer)
* **Dependency Manager:** Composer
* **Frontend:** HTML5, CSS3
* **Server:** Apache (XAMPP/MAMP recommended) or PHP Built-in Server

---

## 🏗️ Architecture & Setup

### Prerequisites
* PHP & Composer installed.
* MySQL Server running (e.g., via XAMPP).
* A web server (Apache/Nginx) or VS Code with PHP Server extension.

### 1. Installation
Extract the project files ensuring the following directory structure is maintained for security:

```text
/root/
├── database/           # SQL installation scripts
├── html/
│   └── website/        # Publicly accessible app files (index.php, etc.)
└── private/            # Secured credentials (config.ini)
````

**Important:** The `private/` folder must sit *outside* your web root to prevent public access to database credentials.

### 2\. Database Configuration

1.  Import `database/installation.sql` into your MySQL server (e.g., via phpMyAdmin).
2.  Open `private/config.ini` and update your credentials:
    ```ini
    [database]
    host = "localhost"
    username = "root"
    password = ""
    dbname = "lancasters_db"
    ```

### 3\. Running the Application

**Option A: VS Code (Recommended for Dev)**

1.  Open the `website` folder in VS Code.
2.  Right-click `index.php` \> **PHP Server: Serve project**.

**Option B: Apache/XAMPP**

1.  Point your document root to the `html/` folder.
2.  Ensure permissions are set for Twig's `cache` and `templates` folders.

### 4\. Initial Setup (Staff Account)

On the first launch, the database has no users.

1.  Navigate to `Login`.
2.  You will see a **Staff Sign Up** form (default state).
3.  Create the admin account.
4.  Log in to access the Staff Dashboard and create the first Service.

-----

## 📬 Contact

**Yusuf Qureshi**
*Connect with me on LinkedIn to discuss this project further.*

  * [LinkedIn Profile](https://www.linkedin.com/in/mohammedyusufqureshi)
