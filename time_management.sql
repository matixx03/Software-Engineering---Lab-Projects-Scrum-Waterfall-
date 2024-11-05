-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 05. Nov 2024 um 22:38
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
-- Datenbank: `time_management`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `day`
--

CREATE TABLE `day` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `weekday` text DEFAULT NULL,
  `time_started` time DEFAULT NULL,
  `time_ended` time DEFAULT NULL,
  `time_worked` time DEFAULT NULL,
  `time_break` time DEFAULT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `day`
--

INSERT INTO `day` (`id`, `date`, `weekday`, `time_started`, `time_ended`, `time_worked`, `time_break`, `comment`) VALUES
(1, '2024-11-03', 'monday', '08:00:00', '16:00:00', '07:30:00', '00:30:00', 'test'),
(2, '2024-11-04', 'wednesday', '08:00:00', '16:00:00', '07:30:00', '00:30:00', ''),
(4, '2024-11-05', 'Monday', '08:30:00', '17:00:00', '08:00:00', '00:30:00', 'Team meeting in the morning'),
(5, '2024-11-06', 'Tuesday', '09:00:00', '18:00:00', '08:30:00', '00:30:00', 'Worked on project deliverables'),
(6, '2024-11-07', 'Wednesday', '08:00:00', '16:30:00', '07:30:00', '01:00:00', 'Client call in the afternoon'),
(7, '2024-11-08', 'Thursday', '08:15:00', '16:15:00', '07:30:00', '00:30:00', 'Research and development tasks'),
(8, '2024-11-09', 'Friday', '09:00:00', '17:30:00', '07:30:00', '01:00:00', 'Training session attended'),
(9, '2024-11-10', 'Saturday', '10:00:00', '14:00:00', '04:00:00', '00:00:00', 'Weekend support shift'),
(10, '2024-11-11', 'Sunday', '12:00:00', '16:00:00', '04:00:00', '00:00:00', 'Weekend data backup tasks'),
(11, '2024-11-12', 'Monday', '08:00:00', '17:00:00', '08:30:00', '00:30:00', 'Worked on quarterly report'),
(12, '2024-11-13', 'Tuesday', '07:30:00', '16:00:00', '08:00:00', '00:30:00', 'Technical documentation updated'),
(13, '2024-11-14', 'Wednesday', '08:00:00', '16:00:00', '08:00:00', '00:00:00', 'No breaks, tight deadlines');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `teamID` int(11) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `working_hours` time DEFAULT NULL,
  `employee_type` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `employee`
--

INSERT INTO `employee` (`id`, `name`, `teamID`, `department`, `working_hours`, `employee_type`) VALUES
(1, 'John Doe', 101, 'Engineering', '08:00:00', 'employee'),
(2, 'Jane Smith', 102, 'Marketing', '08:00:00', 'supervisor');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `month`
--

CREATE TABLE `month` (
  `id` varchar(10) NOT NULL,
  `timesheet_id` int(11) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `sickdays` int(11) DEFAULT 0,
  `vacation_days` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `month`
--

INSERT INTO `month` (`id`, `timesheet_id`, `is_approved`, `sickdays`, `vacation_days`) VALUES
('2024-10', 1, 0, 0, 0),
('2024-11', 2, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `timesheet`
--

CREATE TABLE `timesheet` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `flex_time_account` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Daten für Tabelle `timesheet`
--

INSERT INTO `timesheet` (`id`, `employee_id`, `flex_time_account`) VALUES
(1, 1, '00:00:00'),
(2, 2, '00:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vacation`
--

CREATE TABLE `vacation` (
  `index` int(11) NOT NULL,
  `begin` date NOT NULL,
  `end` date NOT NULL,
  `status` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `vacation`
--

INSERT INTO `vacation` (`index`, `begin`, `end`, `status`) VALUES
(1, '0000-00-00', '0000-00-00', 'test'),
(2, '2024-11-05', '2024-11-07', 'genehmigt'),
(3, '2024-11-05', '2024-11-07', 'abgelehnt');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `day`
--
ALTER TABLE `day`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date` (`date`);

--
-- Indizes für die Tabelle `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `vacation`
--
ALTER TABLE `vacation`
  ADD UNIQUE KEY `index` (`index`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `day`
--
ALTER TABLE `day`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `vacation`
--
ALTER TABLE `vacation`
  MODIFY `index` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
