# 🍽️ Lancaster's Restaurant Booking System

**A full-stack web application built with vanilla PHP and custom HTML/CSS.**

![Project Status](https://img.shields.io/badge/Status-Completed-success)
![Tech Stack](https://img.shields.io/badge/Tech-PHP%20%7C%20MySQL%20%7C%20HTML%2FCSS-blue)
![Design](https://img.shields.io/badge/Design-Custom%20CSS-purple)

---

## 📍 Overview
**Lancaster's Booking System** is a complete restaurant reservation platform built from the ground up. It bridges a raw PHP backend with a custom-designed frontend to manage the entire dining experience.

Unlike projects that rely on heavy frameworks, this application demonstrates a mastery of **core web technologies**. The frontend was built using semantic **HTML5** and vanilla **CSS3**, ensuring a lightweight, accessible, and fully responsive user interface, while the backend handles complex reservation logic and database interactions.

---

## ✨ Key Features

### 🎨 Frontend (Customer Portal)
* **Custom UI Design:** A clean, branded interface built with raw CSS (no Bootstrap/Tailwind).
* **Service Browsing:** Interactive menus to view Breakfast, Lunch, or Dinner availability.
* **Dynamic Forms:** User-friendly booking forms with real-time validation.
* **Responsive Layout:** Optimized for both desktop and mobile screens.

### ⚙️ Backend (Staff Dashboard)
* **Service Management:** Admin tools to configure dining times and capacity.
* **Reservation Tracking:** Complete CRUD operations for managing guest bookings.
* **Secure Auth:** robust login/signup system for staff and customers.
* **Templating:** Utilizes **Twig** for efficient, modular view rendering.

---

## 🛠️ Tech Stack

### Frontend
* **Languages:** HTML5, CSS3
* **Templating:** Twig Engine
* **Design:** Custom Layouts (Flexbox/Grid), Responsive Media Queries

### Backend
* **Language:** PHP (v8.0+)
* **Database:** MySQL
* **Architecture:** MVC Pattern (Model-View-Controller)
* **Dependency Management:** Composer

---

## 🏗️ Architecture & Setup

### Prerequisites
* PHP & Composer installed.
* MySQL Server (e.g., XAMPP/MAMP).
* A web server (Apache/Nginx).

### 1. Installation
Extract the project files while maintaining the security structure:

```text
/root/
├── database/           # SQL installation scripts
├── html/
│   └── website/        # Publicly accessible app (HTML/PHP/CSS)
└── private/            # Secured credentials (config.ini)
````

**Note:** The `private/` folder must reside *outside* your web server's public root to protect database credentials.

### 2\. Database Configuration

1.  Import `database/installation.sql` into your MySQL server via phpMyAdmin.
2.  Configure `private/config.ini`:
    ```ini
    [database]
    host = "localhost"
    username = "root"
    password = ""
    dbname = "lancasters_db"
    ```

### 3\. Running the Application

**Using VS Code (Recommended):**

1.  Open the `website` folder.
2.  Right-click `index.php` \> **PHP Server: Serve project**.

**Using Apache/XAMPP:**

1.  Move the `html/` content to your `htdocs` or `www` folder.
2.  Ensure permissions are set for Twig's `cache` directory.

-----

## 📬 Contact

**Yusuf Qureshi**
*Connect with me on LinkedIn to discuss this project further.*

  * [LinkedIn Profile](https://www.linkedin.com/in/mohammedyusufqureshi)
