-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-07-2025 a las 02:50:20
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
-- Base de datos: `disposicion_aulica`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aulas`
--

CREATE TABLE `aulas` (
  `id_aula` int(11) NOT NULL,
  `piso` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `aulas`
--

INSERT INTO `aulas` (`id_aula`, `piso`, `cantidad`, `numero`) VALUES
(1, 1, 100, '1A'),
(2, 1, 100, '1'),
(3, 1, 100, '4'),
(4, 1, 100, '4A'),
(5, 1, 100, '5'),
(6, 2, 100, '10'),
(7, 2, 100, '11'),
(8, 1, 110, '6'),
(9, 1, 120, 'Auditorio'),
(10, 1, 50, '2'),
(11, 1, 50, '3'),
(12, 2, 50, '7'),
(13, 2, 50, '8'),
(16, 2, 50, 'Laboratorio 1'),
(17, 2, 50, 'Laboratorio 2'),
(18, 2, 50, 'Laboratorio 4'),
(19, 2, 50, '9'),
(20, 2, 50, '8A'),
(21, 2, 50, 'Laboratorio 3'),
(22, 0, 30, 'Biblioteca'),
(23, 0, 10, 'Sala de Reuniones');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `universidad_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera`, `nombre`, `universidad_id`) VALUES
(2, 'Tecnicatura Universitaria en Tecnología de los Alimentos', 3),
(3, 'Tecnicatura Universitaria en Biotecnología', 2),
(4, 'Tecnicatura Universitaria en Diseño Industrial', 3),
(5, 'Tecnicatura Universitaria en Programación', 4),
(6, 'Tecnicatura Universitaria en Producción de Videojuegos', 6),
(7, 'Enfermería Universitaria', 5),
(8, 'Licenciatura en Obstetricia', 3),
(11, 'Diplomatura Desarrollo Web', 5),
(12, 'Diplomatura en Programación y Análisis de datos', 5),
(13, 'Diplomatura en Gestión de TIC para PyMEs', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos_pre_admisiones`
--

CREATE TABLE `cursos_pre_admisiones` (
  `id_curso_pre_admision` int(11) NOT NULL,
  `nombre_curso` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cursos_pre_admisiones`
--

INSERT INTO `cursos_pre_admisiones` (`id_curso_pre_admision`, `nombre_curso`) VALUES
(3, 'Ciclo Introductorio (UNQUI)'),
(4, 'Curso de Preparación Universitaria (UNAHUR)'),
(5, 'Ciclo de Inicio Universitario (UNPAZ)'),
(6, 'Ciclo Básico Común (UBA)'),
(7, 'Curso de Preparación Universitaria (UTN)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias`
--

CREATE TABLE `dias` (
  `id_dia` int(11) NOT NULL,
  `jornada_id` int(11) NOT NULL,
  `itinerario_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `aula_id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `itinerario`
--

CREATE TABLE `itinerario` (
  `id_itinerario` int(11) NOT NULL,
  `hora_fin` time NOT NULL,
  `hora_inicio` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `itinerario`
--

INSERT INTO `itinerario` (`id_itinerario`, `hora_fin`, `hora_inicio`) VALUES
(1, '00:00:00', '00:00:00'),
(2, '14:12:00', '12:22:00'),
(3, '04:32:00', '13:45:00'),
(4, '14:59:00', '13:50:00'),
(5, '15:30:00', '12:30:00'),
(6, '15:30:00', '13:00:00'),
(7, '17:30:00', '15:29:00'),
(8, '17:30:00', '15:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jornada`
--

CREATE TABLE `jornada` (
  `id_jornada` int(11) NOT NULL,
  `dias` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `jornada`
--

INSERT INTO `jornada` (`id_jornada`, `dias`) VALUES
(1, 'Lunes'),
(2, 'Martes'),
(4, 'Jueves'),
(5, 'Viernes'),
(6, 'Sábado'),
(7, 'Domingo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `carrera_id` int(11) DEFAULT NULL,
  `curso_pre_admision_id` int(11) DEFAULT NULL,
  `profesor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id_materia`, `nombre`, `carrera_id`, `curso_pre_admision_id`, `profesor_id`) VALUES
(1, 'Inglés Básico', 3, NULL, NULL),
(2, 'Inglés Técnico', NULL, NULL, 2),
(3, 'Informática', 3, NULL, 2),
(4, 'Técnicas Básicas de Laboratorio', 3, NULL, NULL),
(5, 'Matemática Aplicada', 3, NULL, NULL),
(6, 'Química General', 3, NULL, NULL),
(7, 'Química Orgánica', 3, NULL, NULL),
(8, 'Taller de Física Aplicada', 3, NULL, NULL),
(9, 'Fundamentos en Biología Celular y Molecular', 3, NULL, NULL),
(10, 'Biotecnología Clásica y Moderna', 3, NULL, NULL),
(11, 'Bioquímica', 3, NULL, NULL),
(12, 'Laboratorio de Química Instrumental', 3, NULL, NULL),
(13, 'Bases de la Microbiología Aplicada', 3, NULL, 2),
(14, 'Técnicas Inmunológicas', 3, NULL, NULL),
(15, 'Estadística Aplicada', 3, NULL, NULL),
(16, 'Higiene y Seguridad Industrial', 3, NULL, NULL),
(17, 'Técnicas de Biología Molecular y Genética', 3, NULL, NULL),
(18, 'Producción por Fermentadores', 3, NULL, NULL),
(19, 'Modelos Animales y Bioterio', 3, NULL, NULL),
(20, 'Bioinformática', 3, NULL, NULL),
(21, 'Introducción a la Biotecnología Animal', 3, NULL, NULL),
(22, 'Introducción a la Biotecnología Vegetal', 3, NULL, NULL),
(23, 'Buenas Prácticas de Laboratorio', 3, NULL, NULL),
(24, 'Buenas Prácticas en la Producción Farmacéutica', 3, NULL, NULL),
(25, 'Facturación y Ventas', 3, NULL, NULL),
(26, 'Curso Complementario', 3, NULL, NULL),
(27, 'Introducción a la Tecnología de los Alimentos', 2, NULL, NULL),
(28, 'Matemática I', 2, NULL, NULL),
(29, 'Nuevos Entornos y Lenguajes: la producción del con', 2, NULL, NULL),
(30, 'Biología General', 2, NULL, NULL),
(31, 'Introducción a la Química', 2, NULL, NULL),
(32, 'Higiene y Seguridad', 2, NULL, NULL),
(33, 'Química General e Inorgánica', 2, NULL, NULL),
(34, 'Matemática II', 2, NULL, NULL),
(35, 'Inglés I', 2, NULL, NULL),
(36, 'Asignatura UNAHUR', 2, NULL, NULL),
(37, 'Introducción al Laboratorio de Análisis de Aliment', 2, NULL, NULL),
(38, 'Microbiología General', 2, NULL, NULL),
(39, 'Física', 2, NULL, NULL),
(40, 'Química Orgánica', 2, NULL, NULL),
(41, 'Microbiología de los Alimentos I', 2, NULL, NULL),
(42, 'Fisicoquímica de los Alimentos I', 2, NULL, NULL),
(43, 'Química de los Alimentos', 2, NULL, NULL),
(44, 'Laboratorio de química Instrumental y Analítica', 2, NULL, NULL),
(45, 'Taller de Bromatología y Análisis de la Calidad', 2, NULL, NULL),
(46, 'Gestión de la Calidad e Inocuidad de los Alimentos', 2, NULL, NULL),
(47, 'Operaciones Unitarias I', 2, NULL, NULL),
(48, 'Seminario General de Procesos Productivos de los A', 2, NULL, NULL),
(49, 'Introducción al Diseño', 4, NULL, NULL),
(50, 'Sistemas de representación gráfica', 4, NULL, NULL),
(51, 'Tecnología I', 4, NULL, NULL),
(52, 'Modelado', 4, NULL, NULL),
(53, 'Taller de Diseño I', 4, NULL, NULL),
(54, 'Matemática', 4, NULL, NULL),
(55, 'Morfología I', 4, NULL, NULL),
(56, 'Nuevos entornos y lenguajes: la producción del con', 4, NULL, NULL),
(57, 'Taller de Diseño II', 4, NULL, NULL),
(58, 'Tecnología y sociedad', 4, NULL, NULL),
(59, 'Taller de producción I', 4, NULL, NULL),
(60, 'Tecnología II', 4, NULL, NULL),
(61, 'Programación', 4, NULL, NULL),
(62, 'Ciencias aplicadas al diseño', 4, NULL, NULL),
(63, 'Taller de Diseño III', 4, NULL, NULL),
(64, 'Morfología II', 4, NULL, NULL),
(65, 'Tecnologías de fabricación digital I', 4, NULL, NULL),
(66, 'Asignatura UNAHUR', 4, NULL, NULL),
(67, 'Taller de Diseño IV', 4, NULL, NULL),
(68, 'Tecnología III', 4, NULL, NULL),
(69, 'Tecnologías de fabricación digital II', 4, NULL, NULL),
(70, 'Diseño e industria', 4, NULL, NULL),
(71, 'Inglés I', 4, NULL, NULL),
(72, 'Programación I', 5, NULL, NULL),
(73, 'Sistemas de Procesamientos de datos', 5, NULL, NULL),
(74, 'Matemática', 5, NULL, NULL),
(75, 'Ingles I', 5, NULL, NULL),
(76, 'Laboratorio de Computación I', 5, NULL, NULL),
(77, 'Programación II', 5, NULL, NULL),
(78, 'Arquitectura y Sistemas Operativos', 5, NULL, NULL),
(79, 'Estadística', 5, NULL, NULL),
(80, 'Metodología de la Investigación', 5, NULL, NULL),
(81, 'Inglés II', 5, NULL, NULL),
(82, 'Laboratorio de Computación II', 5, NULL, NULL),
(83, 'Programación III', 5, NULL, NULL),
(84, 'Organización Contable de la Empresa', 5, NULL, NULL),
(85, 'Organización Empresarial', 5, NULL, NULL),
(86, 'Elementos de Investigación Operativa', 5, NULL, NULL),
(87, 'Laboratorio de Computación III', 5, NULL, NULL),
(88, 'Diseño y administración de bases de datos', 5, NULL, NULL),
(89, 'Metodología de Sistemas I', 5, NULL, NULL),
(90, 'Legislación', 5, NULL, NULL),
(91, 'Laboratorio de Computación IV', 5, NULL, NULL),
(92, 'Práctica Profesional', 5, NULL, NULL),
(93, 'Economía de la cultura', 6, NULL, NULL),
(94, 'Cultura lúdica: jugar es humano', 6, NULL, NULL),
(95, 'Inglés II', 6, NULL, NULL),
(96, 'Planificación de negocios', 6, NULL, NULL),
(97, 'Metodología de la investigación II', 6, NULL, NULL),
(98, 'Inglés I', 6, NULL, NULL),
(99, 'Ética y liderazgo', 6, NULL, NULL),
(100, 'Taller proyectual', 6, NULL, NULL),
(101, 'Metodología de la investigación I', 6, NULL, NULL),
(102, 'Taller introductorio al diseño en 3D', 6, NULL, NULL),
(103, 'Q.A. (\"Control de calidad\")', 6, NULL, NULL),
(104, 'Producción y prácticas lúdicas II', 6, NULL, NULL),
(105, 'Internacionalización de proyectos', 6, NULL, NULL),
(106, 'Marketing digital', 6, NULL, NULL),
(107, 'Narrativas transmedia', 6, NULL, NULL),
(108, 'Taller de desarrollo de entornos virtuales', 6, NULL, NULL),
(109, 'Juegos serios II', 6, NULL, NULL),
(110, 'Modelos organizacionales', 6, NULL, NULL),
(111, 'Diseño lúdico II', 6, NULL, NULL),
(112, 'Taller de prototipado digital', 6, NULL, NULL),
(113, 'Taller de diseño y animación en 2D', 6, NULL, NULL),
(114, 'Juegos serios I', 6, NULL, NULL),
(115, 'Industria del videojuego', 6, NULL, NULL),
(116, 'Taller de diseño UIX/GUI', 6, NULL, NULL),
(117, 'Aspectos legales del desarrollo de videojuegos', 6, NULL, NULL),
(118, 'Producción y prácticas lúdicas I', 6, NULL, NULL),
(119, 'Historia de los videojuegos', 6, NULL, NULL),
(120, 'Gestión de proyectos', 6, NULL, NULL),
(121, 'Historia de la cultura II', 6, NULL, NULL),
(122, 'Historia del cine', 6, NULL, NULL),
(123, 'Pensamiento social argentino y latinoamericano', 6, NULL, NULL),
(124, 'Fundamentos de la programación I', 6, NULL, NULL),
(125, 'Diseño lúdico I', 6, NULL, NULL),
(126, 'Fundamentos de la programación II', 6, NULL, NULL),
(127, 'Historia de la cultura I', 6, NULL, NULL),
(128, 'La tecnología y sus usos', 6, NULL, NULL),
(129, 'Literatura y pensamiento', 6, NULL, NULL),
(130, 'Introducción al medio audiovisual', 6, NULL, NULL),
(131, 'Introducción a la comunicación', 6, NULL, NULL),
(132, 'Anatomofisiología', 7, NULL, NULL),
(133, 'Módulo Nº 1 Anatomía', 7, NULL, NULL),
(134, 'Módulo Nº2 Fisiología', 7, NULL, NULL),
(135, 'Química Biológica', 7, NULL, NULL),
(136, 'Física Biológica', 7, NULL, NULL),
(137, 'Introducción a la Enfermería en la Salud Pública', 7, NULL, NULL),
(138, 'Introducción a las Ciencias Psicosociales', 7, NULL, NULL),
(139, 'Enfermería Medica I', 7, NULL, NULL),
(140, 'Deontología I', 7, NULL, NULL),
(141, 'Microbiología y Parasitología', 7, NULL, NULL),
(142, 'Nutrición', 7, NULL, NULL),
(143, 'Enfermería En Salud Pública I', 7, NULL, NULL),
(144, 'Enfermería en Salud Materno Infantil', 7, NULL, NULL),
(145, 'Psicología Evolutiva', 7, NULL, NULL),
(146, 'Enfermería en Salud Mental', 7, NULL, NULL),
(147, 'Enfermería en Salud Pública II', 7, NULL, NULL),
(148, 'Enfermería Médica II', 7, NULL, NULL),
(149, 'Enfermería Quirúrgica', 7, NULL, NULL),
(150, 'Dietoterapia', 7, NULL, NULL),
(151, 'Enfermería Psiquiátrica', 7, NULL, NULL),
(152, 'Deontología II', 7, NULL, NULL),
(153, 'Enfermería Obstétrica', 7, NULL, NULL),
(154, 'Enfermería Pediátrica', 7, NULL, NULL),
(155, 'Introducción a la Administración en Enfermería', 7, NULL, NULL),
(156, 'Introducción a la Salud Comunitaria', 8, NULL, NULL),
(157, 'Anátomo-Fisiología I', 8, NULL, NULL),
(158, 'Genética Humana', 8, NULL, NULL),
(159, 'Introducción a la Obstetricia', 8, NULL, NULL),
(160, 'Anátomo-Fisiología II', 8, NULL, NULL),
(161, 'Salud Comunitaria I', 8, NULL, NULL),
(162, 'Bioquímica', 8, NULL, NULL),
(163, 'Cultura y alfabetización digital en la universidad', 8, NULL, NULL),
(164, 'Introducción a la Nutrición', 8, NULL, NULL),
(165, 'Salud Comunitaria II', 8, NULL, NULL),
(166, 'Obstetricia I', 8, NULL, NULL),
(167, 'Salud Sexual y Reproductiva', 8, NULL, NULL),
(168, 'Obstetricia II', 8, NULL, NULL),
(169, 'Antropología', 8, NULL, NULL),
(170, 'Salud Comunitaria III', 8, NULL, NULL),
(171, 'Asignatura UNAHUR', 8, NULL, NULL),
(172, 'Psicología', 8, NULL, NULL),
(173, 'Obstetricia III', 8, NULL, NULL),
(174, 'Salud Comunitaria IV', 8, NULL, NULL),
(175, 'Deontología y aspectos legales del Ejercicio Profesional', 8, NULL, NULL),
(176, 'Obstetricia patológica', 8, NULL, NULL),
(177, 'Obstetricia IV', 8, NULL, NULL),
(178, 'Preparación Integral para la maternidad', 8, NULL, NULL),
(179, 'Microbiología', 8, NULL, NULL),
(180, 'Farmacología', 8, NULL, NULL),
(181, 'Evaluación de salud fetal', 8, NULL, NULL),
(182, 'Farmacología Obstétrica', 8, NULL, NULL),
(183, 'Práctica obstétrica integrada I', 8, NULL, NULL),
(184, 'Taller de investigación I', 8, NULL, NULL),
(185, 'Salud Comunitaria V', 8, NULL, NULL),
(186, 'Historia Sociosanitaria de la Salud', 8, NULL, NULL),
(187, 'Ética y desarrollo Profesional', 8, NULL, NULL),
(188, 'Puericultura', 8, NULL, NULL),
(189, 'Taller de investigación II', 8, NULL, NULL),
(190, 'Práctica obstétrica integrada II', 8, NULL, NULL),
(211, 'Introducción al Desarrollo Frontend con React', 11, NULL, NULL),
(212, 'Introducción al Desarrollo Backend con Nodejs', 11, NULL, NULL),
(213, 'Diseño UX', 11, NULL, NULL),
(214, 'Proyecto Final de Desarrollo Web', 11, NULL, NULL),
(215, 'Lectura y Escritura Académica', NULL, 3, NULL),
(216, 'Matemática', NULL, 3, NULL),
(217, 'Introducción al Conocimiento de la Física y la Química', NULL, 3, NULL),
(218, 'Pensamiento Matemático', NULL, 4, NULL),
(219, 'Lectura y Escritura', NULL, 4, NULL),
(220, 'Vida Universitaria', NULL, 4, NULL),
(221, 'Programación Inicial', NULL, 7, NULL),
(222, 'Matemática Inicial', NULL, 7, NULL),
(223, 'Lectura Comprensiva', NULL, 7, NULL),
(224, 'Matemática ', NULL, 5, NULL),
(225, 'Lectura y Escritura', NULL, 5, NULL),
(226, 'Sociedad y Vida universitaria', NULL, 5, NULL),
(227, 'Introducción al Pensamiento Científico (IPC)', NULL, 6, NULL),
(228, 'Introducción al Conocimiento de la Sociedad y el Estado (ICSE)', NULL, 6, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id_profesor` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(20) NOT NULL,
  `correo` varchar(40) DEFAULT NULL,
  `telefono` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id_profesor`, `nombre`, `apellido`, `correo`, `telefono`) VALUES
(1, 'Cudi', 'Ejemplo', 'correo@gmail.com', 1122222222),
(2, 'José', 'Salazar', 'profe@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `universidades`
--

CREATE TABLE `universidades` (
  `id_universidad` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `acronimo` varchar(6) NOT NULL,
  `curso_pre_admision_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `universidades`
--

INSERT INTO `universidades` (`id_universidad`, `nombre`, `acronimo`, `curso_pre_admision_id`) VALUES
(2, 'Universidad Nacional de Quilmes', 'UNQUI', 3),
(3, 'Universidad Nacional de Hurlingham', 'UNAHUR', 4),
(4, 'Universidad Tecnológica Nacional', 'UTN', 7),
(5, 'Universidad de Buenos Aires', 'UBA', 6),
(6, 'Universidad Nacional de José C. Paz', 'UNPAZ', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(20) NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `contraseña`) VALUES
(1, 'cudi', '12');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id_aula`);

--
-- Indices de la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD PRIMARY KEY (`id_carrera`),
  ADD KEY `universidad_id` (`universidad_id`);

--
-- Indices de la tabla `cursos_pre_admisiones`
--
ALTER TABLE `cursos_pre_admisiones`
  ADD PRIMARY KEY (`id_curso_pre_admision`);

--
-- Indices de la tabla `dias`
--
ALTER TABLE `dias`
  ADD PRIMARY KEY (`id_dia`),
  ADD UNIQUE KEY `itinerario_id` (`itinerario_id`),
  ADD KEY `jornada_id` (`jornada_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `aula_id` (`aula_id`),
  ADD KEY `profesor_id` (`profesor_id`);

--
-- Indices de la tabla `itinerario`
--
ALTER TABLE `itinerario`
  ADD PRIMARY KEY (`id_itinerario`);

--
-- Indices de la tabla `jornada`
--
ALTER TABLE `jornada`
  ADD PRIMARY KEY (`id_jornada`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `carrera_id` (`carrera_id`),
  ADD KEY `curso_pre_admision_id` (`curso_pre_admision_id`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id_profesor`);

--
-- Indices de la tabla `universidades`
--
ALTER TABLE `universidades`
  ADD PRIMARY KEY (`id_universidad`),
  ADD KEY `curso_pre_admision_id` (`curso_pre_admision_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id_aula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `cursos_pre_admisiones`
--
ALTER TABLE `cursos_pre_admisiones`
  MODIFY `id_curso_pre_admision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `dias`
--
ALTER TABLE `dias`
  MODIFY `id_dia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `itinerario`
--
ALTER TABLE `itinerario`
  MODIFY `id_itinerario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `jornada`
--
ALTER TABLE `jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `universidades`
--
ALTER TABLE `universidades`
  MODIFY `id_universidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carreras`
--
ALTER TABLE `carreras`
  ADD CONSTRAINT `carreras_ibfk_1` FOREIGN KEY (`universidad_id`) REFERENCES `universidades` (`id_universidad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `dias`
--
ALTER TABLE `dias`
  ADD CONSTRAINT `dias_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id_aula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dias_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dias_ibfk_3` FOREIGN KEY (`itinerario_id`) REFERENCES `itinerario` (`id_itinerario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dias_ibfk_4` FOREIGN KEY (`jornada_id`) REFERENCES `jornada` (`id_jornada`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dias_ibfk_5` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materias_ibfk_2` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materias_ibfk_3` FOREIGN KEY (`curso_pre_admision_id`) REFERENCES `cursos_pre_admisiones` (`id_curso_pre_admision`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `universidades`
--
ALTER TABLE `universidades`
  ADD CONSTRAINT `universidades_ibfk_2` FOREIGN KEY (`curso_pre_admision_id`) REFERENCES `cursos_pre_admisiones` (`id_curso_pre_admision`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
