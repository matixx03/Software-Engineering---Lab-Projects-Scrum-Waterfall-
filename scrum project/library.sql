-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 06. Dez 2024 um 12:28
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `library`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `book`
--

CREATE TABLE `book` (
  `ID` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Author` varchar(50) NOT NULL,
  `Year` varchar(50) NOT NULL,
  `Edition` int(11) NOT NULL,
  `Publisher` varchar(50) NOT NULL,
  `Pieces` int(11) NOT NULL,
  `Borrows` int(11) NOT NULL,
  `Rating` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `book`
--

INSERT INTO `book` (`ID`, `Title`, `Author`, `Year`, `Edition`, `Publisher`, `Pieces`, `Borrows`, `Rating`) VALUES
(1, '1984', 'George Orwell', '1949', 1, 'Secker & Warburg', 3, 2, '4.333333333333333'),
(2, 'To Kill a Mockingbird', 'Harper Lee', '1960', 1, 'J.B. Lippincott & Co.', 3, 2, '3.5'),
(3, 'Pride and Prejudice', 'Jane Austen', '1813', 1, 'T. Egerton', 2, 2, '4'),
(4, 'The Great Gatsby', 'F. Scott Fitzgerald', '1925', 1, 'Charles Scribner\'s Sons', 4, 2, '4.5'),
(5, 'Moby Dick', 'Herman Melville', '1851', 1, 'Harper & Brothers', 2, 2, '3.5'),
(6, 'War and Peace', 'Leo Tolstoy', '1869', 1, 'The Russian Messenger', 3, 2, '3.5'),
(7, 'The Catcher in the Rye', 'J.D. Salinger', '1951', 1, 'Little, Brown and Company', 5, 2, '4'),
(8, 'The Hobbit', 'J.R.R. Tolkien', '1937', 1, 'George Allen & Unwin', 3, 2, '3.5'),
(9, 'Fahrenheit 451', 'Ray Bradbury', '1953', 1, 'Ballantine Books', 3, 2, '4'),
(10, 'Jane Eyre', 'Charlotte Brontë', '1847', 1, 'Smith, Elder & Co.', 2, 2, '4'),
(11, 'Brave New World', 'Aldous Huxley', '1932', 1, 'Chatto & Windus', 3, 0, ''),
(12, 'Crime and Punishment', 'Fyodor Dostoevsky', '1866', 1, 'The Russian Messenger', 3, 0, ''),
(13, 'The Odyssey', 'Homer', '-800', 0, 'Various', 5, 0, ''),
(14, 'The Iliad', 'Homer', '-750', 0, 'Various', 5, 0, ''),
(15, 'The Divine Comedy', 'Dante Alighieri', '1320', 1, 'Various', 3, 0, ''),
(16, 'Don Quixote', 'Miguel de Cervantes', '1605', 1, 'Francisco de Robles', 2, 0, '5'),
(17, 'The Brothers Karamazov', 'Fyodor Dostoevsky', '1880', 1, 'The Russian Messenger', 4, 0, ''),
(18, 'Les Misérables', 'Victor Hugo', '1862', 1, 'A. Lacroix, Verboeckhoven & Cie', 3, 0, ''),
(19, 'Anna Karenina', 'Leo Tolstoy', '1877', 1, 'The Russian Messenger', 3, 0, '5'),
(20, 'Ulysses', 'James Joyce', '1922', 1, 'Sylvia Beach', 2, 0, ''),
(21, 'A Game of Thrones', 'George RR Martin', '1996', 1, '	Various', 0, 0, '4'),
(23, 'A Game of Thrones', 'George RR Martin', '2000', -1, 'Various', 1, 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `borrowed`
--

CREATE TABLE `borrowed` (
  `ID` int(11) NOT NULL,
  `Book_ID` int(11) NOT NULL,
  `Borrower_ID` int(11) NOT NULL,
  `Borrow_Date` date NOT NULL,
  `Return_Date` date NOT NULL,
  `Rating` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `borrowed`
--

INSERT INTO `borrowed` (`ID`, `Book_ID`, `Borrower_ID`, `Borrow_Date`, `Return_Date`, `Rating`) VALUES
(1, 1, 1, '2024-01-10', '2024-01-20', '5'),
(2, 2, 2, '2024-02-15', '2024-02-25', '4'),
(3, 3, 3, '2024-03-01', '2024-03-10', '3'),
(4, 4, 4, '2024-04-05', '2024-04-15', '5'),
(5, 5, 5, '2024-05-20', '2024-05-30', '2'),
(6, 6, 6, '2024-06-10', '2024-06-20', '4'),
(7, 7, 7, '2024-07-01', '2024-07-10', '3'),
(8, 8, 8, '2024-08-15', '2024-08-25', '5'),
(9, 9, 9, '2024-09-01', '2024-09-10', '4'),
(10, 10, 10, '2024-10-05', '2024-10-15', '3'),
(11, 1, 2, '2024-01-25', '2024-02-01', '4'),
(12, 2, 3, '2024-02-20', '2024-02-28', '3'),
(13, 3, 4, '2024-03-12', '2024-03-22', '5'),
(14, 4, 5, '2024-04-18', '2024-04-28', '4'),
(15, 5, 6, '2024-05-25', '2024-06-04', '5'),
(16, 6, 7, '2024-06-15', '2024-06-25', '3'),
(17, 7, 8, '2024-07-10', '2024-07-20', '5'),
(18, 8, 9, '2024-08-20', '2024-08-30', '2'),
(19, 9, 10, '2024-09-10', '2024-09-20', '4'),
(20, 10, 1, '2024-10-20', '2024-10-30', '5'),
(21, 1, 1, '2024-11-26', '2024-12-10', '5'),
(22, 1, 1, '2024-11-26', '2024-12-10', '5'),
(23, 1, 1, '2024-11-27', '2024-12-11', '1'),
(24, 16, 1, '2024-11-27', '2024-12-11', '5'),
(25, 16, 1, '2024-11-27', '2024-12-11', '5'),
(26, 19, 1, '2024-11-27', '2024-12-11', '5'),
(27, 19, 1, '2024-11-27', '2024-12-11', '5'),
(28, 19, 1, '2024-11-27', '2024-12-11', '5'),
(29, 1, 1, '2024-11-28', '2024-12-12', '5'),
(30, 21, 1, '2024-11-28', '2024-12-12', '4'),
(31, 1, 1, '2024-12-05', '2024-12-19', ''),
(32, 21, 1, '2024-12-05', '2024-12-19', ''),
(33, 9, 1, '2024-12-05', '2024-12-19', ''),
(34, 12, 1, '2024-12-05', '2024-12-19', ''),
(35, 1, 2, '2024-12-05', '2024-12-19', ''),
(36, 1, 4, '2024-12-06', '2024-12-20', '5'),
(37, 1, 4, '2024-12-06', '2024-12-20', '5'),
(38, 1, 4, '2024-12-06', '2024-12-20', '4');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `borrower`
--

CREATE TABLE `borrower` (
  `ID` int(11) NOT NULL,
  `Surname` varchar(50) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `E_mail` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `borrower`
--

INSERT INTO `borrower` (`ID`, `Surname`, `Name`, `E_mail`, `password`, `role`) VALUES
(1, 'Smith', 'John', 'john.smith@example.com', 'password', 'librarian'),
(2, 'Doe', 'Jane', 'jane.doe@example.com', 'password', 'librarian'),
(3, 'Johnson', 'Robert', 'robert.johnson@example.com', 'password', 'librarian'),
(4, 'Brown', 'Emily', 'emily.brown@example.com', 'password', 'user'),
(5, 'Davis', 'Michael', 'michael.davis@example.com', 'password', 'user'),
(6, 'Garcia', 'Maria', 'maria.garcia@example.com', 'password', 'user'),
(7, 'Martinez', 'David', 'david.martinez@example.com', 'password', 'user'),
(8, 'Rodriguez', 'Sarah', 'sarah.rodriguez@example.com', 'password', 'user'),
(9, 'Lee', 'Daniel', 'daniel.lee@example.com', 'password', 'user'),
(10, 'Taylor', 'Jessica', 'jessica.taylor@example.com', 'password', 'user');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `borrowed`
--
ALTER TABLE `borrowed`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `borrower`
--
ALTER TABLE `borrower`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `book`
--
ALTER TABLE `book`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `borrowed`
--
ALTER TABLE `borrowed`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT für Tabelle `borrower`
--
ALTER TABLE `borrower`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
