-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2026 at 02:50 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `decorum_bookshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(150) NOT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `category` varchar(80) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `year_published` year(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `title`, `author`, `publisher`, `category`, `price`, `stock_quantity`, `year_published`, `created_at`, `updated_at`) VALUES
(1, '9780061120084', 'To Kill a Mockingbird', 'Harper Lee', 'HarperCollins', 'Fiction', 1200.00, 142, '1960', '2026-06-04 20:19:12', '2026-06-04 20:19:12'),
(2, '9780743273565', 'The Great Gatsby', 'F. Scott Fitzgerald', 'Scribner', 'Classic', 950.00, 20, '1925', '2026-06-04 20:19:12', '2026-06-09 18:00:09'),
(3, '9780140283297', 'Things Fall Apart', 'Chinua Achebe', 'Penguin Books', 'African Lit', 880.00, 87, '1958', '2026-06-04 20:19:12', '2026-06-04 20:19:12'),
(5, '9780199535569', 'The River Between', 'Ngugi wa Thiongo', 'Heinemann', 'African Lit', 780.00, 54, '1965', '2026-06-04 20:19:12', '2026-06-04 20:19:12'),
(6, '9780141439518', 'Pride and Prejudice', 'Jane Austen', 'Penguin Classics', 'Classic', 900.00, 30, '0000', '2026-06-04 20:19:12', '2026-06-09 18:00:20'),
(7, '9780316769174', 'The Catcher in the Rye', 'J.D. Salinger', 'Little, Brown', 'Fiction', 990.00, 15, '1951', '2026-06-04 20:19:12', '2026-06-04 20:19:12'),
(9, '9780525559474', 'The Hunger Games', 'Suzanne Collins', 'Scholastic Press', 'YA Fiction', 1050.00, 91, '2008', '2026-06-04 20:19:12', '2026-06-04 20:19:12'),
(10, '9780385737951', 'The Maze Runner', 'James Dashner', 'Delacorte Press', 'YA Fiction', 980.00, 9, '2009', '2026-06-04 20:19:12', '2026-06-04 20:19:12'),
(12, '9789966657046', 'Four Figure Mathematical Tables', 'Kenya National Council', 'KICD', 'Education', 650.00, 10, '2021', '2026-06-09 18:12:56', '2026-06-09 18:12:56');

-- --------------------------------------------------------

--
-- Table structure for table `requisitions`
--

CREATE TABLE `requisitions` (
  `id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requisitions`
--

INSERT INTO `requisitions` (`id`, `manager_id`, `date_submitted`, `status`, `notes`) VALUES
(1, 2, '2026-06-05 04:10:18', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `requisition_items`
--

CREATE TABLE `requisition_items` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity_requested` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager') NOT NULL DEFAULT 'manager',
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `full_name`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', 'System Administrator', '2026-06-04 20:19:12'),
(2, 'manager1', 'pass123', 'manager', 'Jane Mwangi', '2026-06-04 20:19:12'),
(3, 'manager2', 'pass123', 'manager', 'David Kamau', '2026-06-04 20:19:12'),
(4, 'manager3', 'pass123', 'manager', 'Aisha Omar', '2026-06-04 20:19:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_isbn` (`isbn`);

--
-- Indexes for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_w4_req_manager` (`manager_id`);

--
-- Indexes for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_w4_item_req` (`requisition_id`),
  ADD KEY `fk_w4_item_book` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `requisitions`
--
ALTER TABLE `requisitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `requisition_items`
--
ALTER TABLE `requisition_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD CONSTRAINT `fk_w4_req_manager` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD CONSTRAINT `fk_w4_item_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_w4_item_req` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
