# Decorum Bookshop - B2B Inventory Portal

## Project Description
A full-stack, role-based web application designed to manage B2B book requisitions and master inventory. Built using PHP and MySQL, this system features robust session-based authentication, complete CRUD operations, and a dynamic dashboard that reflects real-time database state using SQL aggregate functions.

## Features
* **Role-Based Access Control (RBAC):** Strict separation between Administrator and Branch Manager privileges.
* **Master Inventory Management:** Secure CRUD operations (Create, Read, Update, Delete) utilizing PDO Prepared Statements to prevent SQL injection.
* **Transactional Security:** Requisition submissions utilize MySQL InnoDB transactions (`BEGIN`, `COMMIT`, `ROLLBACK`) to ensure data integrity.
* **Dynamic Dashboards:** Real-time calculation of total stock, orders, and estimated revenue.
* **Security:** Password hashing (`password_hash`), session protection, and anti-CSRF measures.

## Prerequisites
To run this application locally, you will need:
* A local server environment like **XAMPP**, **WAMP**, or **MAMP**.
* PHP 7.4 or higher.
* MySQL / MariaDB.

## Installation & Setup Instructions
1. **Clone the Repository:** Download or clone this repository and place the folder inside your local server's web directory (e.g., `C:\xampp\htdocs\decorum_bookshop`).
2. **Start the Server:** Open XAMPP Control Panel and start **Apache** and **MySQL**.
3. **Database Setup:**
   * Open your browser and navigate to `http://localhost/phpmyadmin`.
   * Create a new database named `decorum_bookshop`.
   * Click on the **Import** tab.
   * Choose the `decorum_bookshop.sql` file included in this repository and click **Import** to build the tables and insert the seed data.
4. **Database Configuration:** If your MySQL setup uses a password, update the credentials inside the `includes/db_connect.php` file.
5. **Launch:** Navigate to `http://localhost/decorum_bookshop/login.php` in your web browser.

## Test Credentials
To evaluate the system's Role-Based Access Control, please use the following seeded accounts:

**Administrator Account:**
* **Username:** admin
* **Password:** admin123
* *Access:* Master catalog CRUD operations, full statistical dashboard.

**Manager Account:**
* **Username:** manager1
* **Password:** pass123
* *Access:* View catalog, submit requisition orders, view order history.
