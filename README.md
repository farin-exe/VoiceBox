## 1. Project Title

**VoiceBox — Student Complaint Management System**

## 2. Project Description

VoiceBox is a dynamic full-stack web application developed to provide university students with a simple and organized platform for submitting and tracking complaints. Students can create accounts, log in securely, submit complaints under different categories, monitor complaint statuses, withdraw eligible complaints, and delete withdrawn or rejected complaints.

The system also provides a dedicated administrator login and administration panel where authorized administrators can review complaints, update their status, add administrative notes, manage complaint categories, and delete inappropriate or invalid complaint records.

The application demonstrates the complete full-stack workflow:

**HTML → CSS → JavaScript → PHP → MySQL**

## 3. Group Information

**Group Number:** 6

### Group Members

1. Farhana Akter (ID: 2023200000644)
2. Sadiya Yasmin (ID: 2023200000650)
3. Farin Anjum (ID: 2023200000656)
4. S M Saleh Ahmed (ID: 2023200000680)

## 4. Technologies Used

* **HTML** — Web page structure and forms
* **CSS** — Styling, responsive layout, and visual design
* **JavaScript** — Client-side validation, interactive features, search, navigation, and confirmation dialogs
* **PHP** — Server-side processing, authentication, sessions, validation, CRUD operations, and database communication
* **MySQL** — Database management and storage of users, categories, and complaints
* **PDO** — Secure PHP-to-MySQL database connection and prepared statements
* **XAMPP** — Local Apache and MySQL development environment

## 5. Project Folder Structure

VoiceBox/
│
├── index.php
├── auth.php
├── admin-login.php
├── submit.php
├── complaints.php
├── admin.php
├── logout.php
├── setup_admin.php
├──  database/
│   └── database.sql
├── README.md
│
├── config/
│   └── db.php
│
├── includes/
│   ├── auth.php
│   ├── header.php
│   └── footer.php
│
└── assets/
    ├── css/
    │   └── style.css
    │
    ├── js/
    │   └── app.js
    │
    └── images/
        └── logo.jpg

### Important Files and Folders

**index.php**
The public home page of VoiceBox. It introduces the application, explains the complaint process, and provides navigation to the authentication system.

**auth.php**
Handles student registration and student login using PHP sessions and password hashing.

**admin-login.php**
Provides a separate administrator login interface. Only users whose database role is admin can authenticate through this interface.

**submit.php**
Allows authenticated students to submit complaints by selecting a category and providing a subject and description.

**complaints.php**
Displays the logged-in student's complaint history, current statuses, administrative notes, and available actions.

**admin.php**
The administrator control panel for viewing complaints, updating statuses, adding administrative notes, deleting complaint records, and managing categories.

**logout.php**
Destroys the current session and logs the user out.

**setup_admin.php**
Creates the predefined administrator account. It should be deleted after the initial administrator account has been created.

**database.sql**
Contains the SQL commands required to create the VoiceBox database, tables, relationships, and default complaint categories.

**config/db.php**
Contains the PDO database connection configuration.

**includes/auth.php**
Contains authentication/session helper functions, access-control functions, CSRF protection, and output escaping.

**includes/header.php and includes/footer.php**
Provide common website layout elements such as navigation and footer sections.

**assets/css/style.css**
Contains the visual styling, responsive layout, buttons, cards, tables, forms, status indicators, and other UI components.

**assets/js/app.js**
Provides client-side interactive functionality such as navigation controls, confirmation dialogs, character counting, table searching, and form validation.

## 6. Database Information

### Database Name

voicebox_db

### Main Tables

| Table        | Purpose                                                                     |
| ------------ | --------------------------------------------------------------------------- |
| users      | Stores student and administrator account information                        |
| categories | Stores complaint categories                                                 |
| complaints | Stores submitted complaints, statuses, administrative notes, and timestamps |

### Main Relationships

* One user can submit multiple complaints.
* Each complaint belongs to one student.
* Each complaint belongs to one complaint category.
* The 'role' field in 'users' distinguishes students from administrators.

## 7. How to Run the Project

### Step 1 — Install XAMPP

Install XAMPP with Apache, PHP, and MySQL.

### Step 2 — Start Services

Open the XAMPP Control Panel and start:

* Apache
* MySQL

### Step 3 — Copy the Project

Place the 'VoiceBox' folder inside:

C:\xampp\htdocs\

The important file should be located at:

C:\xampp\htdocs\VoiceBox\index.php

### Step 4 — Create the Database

Open phpMyAdmin:

http://localhost:8080/phpmyadmin

If your Apache/MySQL configuration uses another port, use the corresponding port.

Select **Import** and import:

VoiceBox/database/database.sql

This creates:

voicebox_db

with the required tables and default complaint categories.

### Step 5 — Check Database Configuration

Open:

config/db.php

The default XAMPP configuration is:

Host: localhost
Database: voicebox_db
Username: root
Password: empty

If the local MySQL root account has a password, update 'config/db.php'.

### Step 6 — Create the Administrator Account

Open:

http://localhost:8080/VoiceBox/setup_admin.php

The predefined administrator account is:

Email: admin@voicebox.com
Password: Admin@123

After successfully creating the administrator account, **delete 'setup_admin.php'** for security.

### Step 7 — Open the Application

Open:

http://localhost:8080/VoiceBox/

Do not open PHP files by double-clicking them or by using VS Code Live Server. PHP must run through Apache/XAMPP.

## 8. Login Credentials

### Administrator Demo Account

Email: admin@voicebox.com
Password: Admin@123

Students do not use a predefined account. They can create their own account through the student registration form.

## 9. Important Notes and Dependencies

* XAMPP with Apache, PHP, and MySQL is required for local execution.
* The application uses PHP sessions for authentication.
* Passwords are stored using PHP's 'password_hash()' mechanism and verified using 'password_verify()'.
* PDO prepared statements are used for database operations.
* CSRF tokens are used for protected POST operations.
* Student and administrator access are controlled according to the user's database role.
* The administrator should use the dedicated administrator login interface.
* 'setup_admin.php' should be deleted after creating the administrator account.
* The application should be accessed through Apache rather than VS Code Live Server.
* The project requires the 'voicebox_db' MySQL database to function correctly.