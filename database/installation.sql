CREATE DATABASE IF NOT EXISTS Lancasters;

USE Lancasters;

CREATE TABLE IF NOT EXISTS Accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_staff BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS Reservations (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service ENUM('breakfast', 'lunch', 'dinner') NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests INT NOT NULL CHECK (guests BETWEEN 1 AND 6),
    additional_info TEXT
);

CREATE TABLE IF NOT EXISTS Services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    name ENUM('breakfast', 'lunch', 'dinner') NOT NULL,
    date DATE NOT NULL,
    max_tables INT NOT NULL CHECK (max_tables > 0)
);

CREATE TABLE IF NOT EXISTS Service_Times (
    service_id INT NOT NULL,
    time TIME NOT NULL,
    num_of_tables INT NOT NULL
);
