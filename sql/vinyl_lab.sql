-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-02-2026 a las 19:43:32
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `vinyl_lab`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `pass`, `email`, `creado_en`, `ultimo_acceso`) VALUES
(1, 'iker', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'iker@vinyllab.com', '2026-02-02 17:58:19', NULL),
(2, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@vinyllab.com', '2026-02-02 17:58:19', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vinilos`
--

CREATE TABLE `vinilos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `artista` varchar(150) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `anio` int(11) NOT NULL,
  `imagen` varchar(500) NOT NULL,
  `visible` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vinilos`
--

INSERT INTO `vinilos` (`id`, `nombre`, `artista`, `descripcion`, `precio`, `anio`, `imagen`, `visible`, `creado_en`, `actualizado_en`) VALUES
(1, 'Thriller', 'Michael Jackson', 'Thriller es el álbum más vendido de todos los tiempos y una obra maestra del pop producida por Quincy Jones. Con clásicos como \"Billie Jean\", \"Beat It\" y \"Thriller\", marcó un antes y un después en la música moderna.', 29.99, 1982, 'uploads/vinilo_thriller.jpg', 1, '2026-02-02 17:58:19', '2026-02-02 17:58:19'),
(2, 'Forever', 'Puff Daddy', 'Forever es un álbum clave de la era Bad Boy, consolidando la posición de Diddy como productor y magnate, rodeado de talento y entregando un sonido pulido que resonó fuertemente a finales de los 90.', 69.67, 1999, 'uploads/vinilo_forever.jpg', 1, '2026-02-02 17:58:19', '2026-02-02 17:58:19'),
(3, 'Abbey Road', 'The Beatles', 'Abbey Road es el undécimo álbum de estudio de The Beatles. Grabado en los estudios de Abbey Road, es considerado una de las mejores obras de la banda y contiene clásicos como \"Come Together\" y \"Here Comes the Sun\".', 224.99, 1969, 'uploads/vinilo_abbey_road.jpg', 1, '2026-02-02 17:58:19', '2026-02-02 17:58:19'),
(4, 'The Dark Side of the Moon', 'Pink Floyd', 'Uno de los álbumes más icónicos de la historia del rock progresivo. Explora temas universales como el tiempo, la muerte y la locura con una producción revolucionaria.', 189.99, 1973, 'uploads/vinilo_darkside.jpg', 1, '2026-02-02 17:58:19', '2026-02-02 17:58:19'),
(5, 'Hot Space', 'Queen', 'Hot Space representa la incursión de Queen en el funk, disco y R&B. Aunque fue controvertido, incluye el hit \"Under Pressure\" con David Bowie.', 149.99, 1982, 'uploads/vinilo_hotspace.jpg', 1, '2026-02-02 17:58:19', '2026-02-02 17:58:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nombre` (`nombre`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indices de la tabla `vinilos`
--
ALTER TABLE `vinilos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visible` (`visible`),
  ADD KEY `idx_anio` (`anio`),
  ADD KEY `idx_nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vinilos`
--
ALTER TABLE `vinilos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
