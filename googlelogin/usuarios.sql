-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 15, 2024 at 02:13 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `usuarios`
--

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `google_id` text COLLATE utf8mb4_general_ci,
  `nombre` text COLLATE utf8mb4_general_ci,
  `email` text COLLATE utf8mb4_general_ci,
  `password` text COLLATE utf8mb4_general_ci,
  `fecha` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `google_id`, `nombre`, `email`, `password`, `fecha`) VALUES
(11, '111908145540850269900', 'Federico Di Pietro', 'bigfndp2005@gmail.com', '$2y$10$.xTujuAfHhcBEgXz/veb/e3.oPVArnbaaY//4tctQn4uTVU9TkcJK', '2024-06-15 11:11:06'),
(13, NULL, 'Ignacio Coquette', 'igncoq1@hotmail.com', '$2y$10$K/2R23MTTkG1iBdgWzZsbeMziPmPEMdQQ.XuqdtkLTlZePEbdBxyC', '2024-06-15 11:11:22'),
(15, NULL, 'tomi123', 'tomiazam2@gmail.com', '$2y$10$D.3F/cZ0kUC6HZdizkT7TuIASzY.uKBR9olF9pZRvTohJqMOOcC5e', '2024-06-15 11:05:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
