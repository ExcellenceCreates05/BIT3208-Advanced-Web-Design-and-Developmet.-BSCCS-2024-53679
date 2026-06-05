-- =============================================================
-- DECORUM BOOKSHOP — B2B INVENTORY SYSTEM
-- Week4db.sql — Database State at end of Week 4
-- =============================================================
-- Week 4 adds: requisitions and requisition_items tables
-- Login is now DB-backed (vs hardcoded credentials in Week 3)
-- =============================================================

DROP DATABASE IF EXISTS decorum_bookshop;

CREATE DATABASE decorum_bookshop
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE decorum_bookshop;

CREATE TABLE users (
    id         INT          NOT NULL AUTO_INCREMENT,
    username   VARCHAR(50)  NOT NULL,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin','manager') NOT NULL DEFAULT 'manager',
    full_name  VARCHAR(100) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE books (
    id             INT           NOT NULL AUTO_INCREMENT,
    isbn           VARCHAR(20)   NOT NULL,
    title          VARCHAR(255)  NOT NULL,
    author         VARCHAR(150)  NOT NULL,
    publisher      VARCHAR(150)  DEFAULT NULL,
    category       VARCHAR(80)   DEFAULT NULL,
    price          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_quantity INT           NOT NULL DEFAULT 0,
    year_published YEAR          DEFAULT NULL,
    created_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_isbn (isbn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE requisitions (
    id             INT      NOT NULL AUTO_INCREMENT,
    manager_id     INT      NOT NULL,
    date_submitted TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    notes          TEXT     DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_w4_req_manager FOREIGN KEY (manager_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE requisition_items (
    id                 INT NOT NULL AUTO_INCREMENT,
    requisition_id     INT NOT NULL,
    book_id            INT NOT NULL,
    quantity_requested INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    CONSTRAINT fk_w4_item_req  FOREIGN KEY (requisition_id) REFERENCES requisitions (id) ON DELETE CASCADE,
    CONSTRAINT fk_w4_item_book FOREIGN KEY (book_id)        REFERENCES books (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Plain-text passwords for Week 4 (login.php handles both plain + hashed)
INSERT INTO users (username, password, role, full_name) VALUES
('admin',    'admin123', 'admin',   'System Administrator'),
('manager1', 'pass123',  'manager', 'Jane Mwangi'),
('manager2', 'pass123',  'manager', 'David Kamau'),
('manager3', 'pass123',  'manager', 'Aisha Omar');

INSERT INTO books (isbn, title, author, publisher, category, price, stock_quantity, year_published) VALUES
('9780061120084', 'To Kill a Mockingbird', 'Harper Lee',           'HarperCollins',   'Fiction',     1200.00, 142, 1960),
('9780743273565', 'The Great Gatsby',       'F. Scott Fitzgerald', 'Scribner',        'Classic',      950.00,  23, 1925),
('9780140283297', 'Things Fall Apart',      'Chinua Achebe',       'Penguin Books',   'African Lit',  880.00,  87, 1958),
('9780452284234', '1984',                   'George Orwell',       'Signet Classic',  'Dystopia',    1050.00,   6, 1949),
('9780199535569', 'The River Between',      'Ngugi wa Thiongo',    'Heinemann',       'African Lit',  780.00,  54, 1965),
('9780141439518', 'Pride and Prejudice',    'Jane Austen',         'Penguin Classics', 'Classic',     900.00,  38, 1813),
('9780316769174', 'The Catcher in the Rye', 'J.D. Salinger',       'Little, Brown',   'Fiction',      990.00,  15, 1951),
('9781501156700', 'It Ends with Us',        'Colleen Hoover',      'Atria Books',     'Romance',     1100.00,  62, 2016),
('9780525559474', 'The Hunger Games',       'Suzanne Collins',     'Scholastic Press','YA Fiction',  1050.00,  91, 2008),
('9780385737951', 'The Maze Runner',        'James Dashner',       'Delacorte Press', 'YA Fiction',   980.00,   9, 2009);

SELECT 'Week4db ready' AS status;
