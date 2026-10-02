CREATE DATABASE IF NOT EXISTS voicebox_db
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE voicebox_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    student_id VARCHAR(50) UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    category_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Pending','In Progress','Resolved','Rejected','Withdrawn') NOT NULL DEFAULT 'Pending',
    admin_note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_complaints_student
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_complaints_category
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO categories (name) VALUES
('Academic'),
('Hostel'),
('Transport'),
('Library'),
('IT / Technical'),
('Other');
