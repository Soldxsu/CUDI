-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-06-2025 a las 19:11:09
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.1.17

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
(21, 2, 50, 'Laboratorio 3');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carreras`
--

CREATE TABLE `carreras` (
  `id_carrera` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `carreras`
--

INSERT INTO `carreras` (`id_carrera`, `nombre`) VALUES
(2, 'Tecnicatura Universitaria en Tecnología de los Alimentos'),
(3, 'Tecnicatura Universitaria en Biotecnología'),
(4, 'Tecnicatura Universitaria en Diseño Industrial'),
(5, 'Tecnicatura Universitaria en Programación'),
(6, 'Tecnicatura Universitaria en Producción de Videojuegos'),
(7, 'Enfermería Universitaria'),
(8, 'Licenciatura en Obstetricia'),
(9, 'Diplomatura en Diseño e Impresión 3D'),
(10, 'Diplomatura Inteligencia Artificial'),
(11, 'Diplomatura Desarrollo Web');

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
(6, 'Ciclo Básico Común (UBA)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dias`
--

CREATE TABLE `dias` (
  `id_dia` int(11) NOT NULL,
  `jornada_id` int(11) NOT NULL,
  `itinerario_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `aula_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `itinerario`
--

CREATE TABLE `itinerario` (
  `id_itinerario` int(11) NOT NULL,
  `horario` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `itinerario`
--

INSERT INTO `itinerario` (`id_itinerario`, `horario`) VALUES
(1, '01:00:00');

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
(3, 'Martes'),
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
  `nombre` varchar(70) NOT NULL,
  `carrera_id` int(11) DEFAULT NULL,
  `curso_pre_admision_id` int(11) DEFAULT NULL,
  `profesor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id_materia`, `nombre`, `carrera_id`, `curso_pre_admision_id`, `profesor_id`) VALUES
(1, 'Inglés Básico', 3, NULL, 1),
(2, 'Inglés Técnico', 3, NULL, 1),
(3, 'Informática', 3, NULL, 1),
(4, 'Técnicas Básicas de Laboratorio', 3, NULL, 1),
(5, 'Matemática Aplicada', 3, NULL, 1),
(6, 'Química General', 3, NULL, 1),
(7, 'Química Orgánica', 3, NULL, 1),
(8, 'Taller de Física Aplicada', 3, NULL, 1),
(9, 'Fundamentos en Biología Celular y Molecular', 3, NULL, 1),
(10, 'Biotecnología Clásica y Moderna', 3, NULL, 1),
(11, 'Bioquímica', 3, NULL, 1),
(12, 'Laboratorio de Química Instrumental', 3, NULL, 1),
(13, 'Bases de la Microbiología Aplicada', 3, NULL, 1),
(14, 'Técnicas Inmunológicas', 3, NULL, 1),
(15, 'Estadística Aplicada', 3, NULL, 1),
(16, 'Higiene y Seguridad Industrial', 3, NULL, 1),
(17, 'Técnicas de Biología Molecular y Genética', 3, NULL, 1),
(18, 'Producción por Fermentadores', 3, NULL, 1),
(19, 'Modelos Animales y Bioterio', 3, NULL, 1),
(20, 'Bioinformática', 3, NULL, 1),
(21, 'Introducción a la Biotecnología Animal', 3, NULL, 1),
(22, 'Introducción a la Biotecnología Vegetal', 3, NULL, 1),
(23, 'Buenas Prácticas de Laboratorio', 3, NULL, 1),
(24, 'Buenas Prácticas en la Producción Farmacéutica', 3, NULL, 1),
(25, 'Facturación y Ventas', 3, NULL, 1),
(26, 'Curso Complementario', 3, NULL, 1),
(27, 'Introducción a la Tecnología de los Alimentos', 2, NULL, 1),
(28, 'Matemática I', 2, NULL, 1),
(29, 'Nuevos Entornos y Lenguajes: la producción del con', 2, NULL, 1),
(30, 'Biología General', 2, NULL, 1),
(31, 'Introducción a la Química', 2, NULL, 1),
(32, 'Higiene y Seguridad', 2, NULL, 1),
(33, 'Química General e Inorgánica', 2, NULL, 1),
(34, 'Matemática II', 2, NULL, 1),
(35, 'Inglés I', 2, NULL, 1),
(36, 'Asignatura UNAHUR', 2, NULL, 1),
(37, 'Introducción al Laboratorio de Análisis de Aliment', 2, NULL, 1),
(38, 'Microbiología General', 2, NULL, 1),
(39, 'Física', 2, NULL, 1),
(40, 'Química Orgánica', 2, NULL, 1),
(41, 'Microbiología de los Alimentos I', 2, NULL, 1),
(42, 'Fisicoquímica de los Alimentos I', 2, NULL, 1),
(43, 'Química de los Alimentos', 2, NULL, 1),
(44, 'Laboratorio de química Instrumental y Analítica', 2, NULL, 1),
(45, 'Taller de Bromatología y Análisis de la Calidad', 2, NULL, 1),
(46, 'Gestión de la Calidad e Inocuidad de los Alimentos', 2, NULL, 1),
(47, 'Operaciones Unitarias I', 2, NULL, 1),
(48, 'Seminario General de Procesos Productivos de los A', 2, NULL, 1),
(49, 'Introducción al Diseño', 4, NULL, 1),
(50, 'Sistemas de representación gráfica', 4, NULL, 1),
(51, 'Tecnología I', 4, NULL, 1),
(52, 'Modelado', 4, NULL, 1),
(53, 'Taller de Diseño I', 4, NULL, 1),
(54, 'Matemática', 4, NULL, 1),
(55, 'Morfología I', 4, NULL, 1),
(56, 'Nuevos entornos y lenguajes: la producción del con', 4, NULL, 1),
(57, 'Taller de Diseño II', 4, NULL, 1),
(58, 'Tecnología y sociedad', 4, NULL, 1),
(59, 'Taller de producción I', 4, NULL, 1),
(60, 'Tecnología II', 4, NULL, 1),
(61, 'Programación', 4, NULL, 1),
(62, 'Ciencias aplicadas al diseño', 4, NULL, 1),
(63, 'Taller de Diseño III', 4, NULL, 1),
(64, 'Morfología II', 4, NULL, 1),
(65, 'Tecnologías de fabricación digital I', 4, NULL, 1),
(66, 'Asignatura UNAHUR', 4, NULL, 1),
(67, 'Taller de Diseño IV', 4, NULL, 1),
(68, 'Tecnología III', 4, NULL, 1),
(69, 'Tecnologías de fabricación digital II', 4, NULL, 1),
(70, 'Diseño e industria', 4, NULL, 1),
(71, 'Inglés I', 4, NULL, 1),
(72, 'Programación I', 5, NULL, 1),
(73, 'Sistemas de Procesamientos de datos', 5, NULL, 1),
(74, 'Matemática', 5, NULL, 1),
(75, 'Ingles I', 5, NULL, 1),
(76, 'Laboratorio de Computación I', 5, NULL, 1),
(77, 'Programación II', 5, NULL, 1),
(78, 'Arquitectura y Sistemas Operativos', 5, NULL, 1),
(79, 'Estadística', 5, NULL, 1),
(80, 'Metodología de la Investigación', 5, NULL, 1),
(81, 'Inglés II', 5, NULL, 1),
(82, 'Laboratorio de Computación II', 5, NULL, 1),
(83, 'Programación III', 5, NULL, 1),
(84, 'Organización Contable de la Empresa', 5, NULL, 1),
(85, 'Organización Empresarial', 5, NULL, 1),
(86, 'Elementos de Investigación Operativa', 5, NULL, 1),
(87, 'Laboratorio de Computación III', 5, NULL, 1),
(88, 'Diseño y administración de bases de datos', 5, NULL, 1),
(89, 'Metodología de Sistemas I', 5, NULL, 1),
(90, 'Legislación', 5, NULL, 1),
(91, 'Laboratorio de Computación IV', 5, NULL, 1),
(92, 'Práctica Profesional', 5, NULL, 1),
(93, 'Economía de la cultura', 6, NULL, 1),
(94, 'Cultura lúdica: jugar es humano', 6, NULL, 1),
(95, 'Inglés II', 6, NULL, 1),
(96, 'Planificación de negocios', 6, NULL, 1),
(97, 'Metodología de la investigación II', 6, NULL, 1),
(98, 'Inglés I', 6, NULL, 1),
(99, 'Ética y liderazgo', 6, NULL, 1),
(100, 'Taller proyectual', 6, NULL, 1),
(101, 'Metodología de la investigación I', 6, NULL, 1),
(102, 'Taller introductorio al diseño en 3D', 6, NULL, 1),
(103, 'Q.A. (\"Control de calidad\")', 6, NULL, 1),
(104, 'Producción y prácticas lúdicas II', 6, NULL, 1),
(105, 'Internacionalización de proyectos', 6, NULL, 1),
(106, 'Marketing digital', 6, NULL, 1),
(107, 'Narrativas transmedia', 6, NULL, 1),
(108, 'Taller de desarrollo de entornos virtuales', 6, NULL, 1),
(109, 'Juegos serios II', 6, NULL, 1),
(110, 'Modelos organizacionales', 6, NULL, 1),
(111, 'Diseño lúdico II', 6, NULL, 1),
(112, 'Taller de prototipado digital', 6, NULL, 1),
(113, 'Taller de diseño y animación en 2D', 6, NULL, 1),
(114, 'Juegos serios I', 6, NULL, 1),
(115, 'Industria del videojuego', 6, NULL, 1),
(116, 'Taller de diseño UIX/GUI', 6, NULL, 1),
(117, 'Aspectos legales del desarrollo de videojuegos', 6, NULL, 1),
(118, 'Producción y prácticas lúdicas I', 6, NULL, 1),
(119, 'Historia de los videojuegos', 6, NULL, 1),
(120, 'Gestión de proyectos', 6, NULL, 1),
(121, 'Historia de la cultura II', 6, NULL, 1),
(122, 'Historia del cine', 6, NULL, 1),
(123, 'Pensamiento social argentino y latinoamericano', 6, NULL, 1),
(124, 'Fundamentos de la programación I', 6, NULL, 1),
(125, 'Diseño lúdico I', 6, NULL, 1),
(126, 'Fundamentos de la programación II', 6, NULL, 1),
(127, 'Historia de la cultura I', 6, NULL, 1),
(128, 'La tecnología y sus usos', 6, NULL, 1),
(129, 'Literatura y pensamiento', 6, NULL, 1),
(130, 'Introducción al medio audiovisual', 6, NULL, 1),
(131, 'Introducción a la comunicación', 6, NULL, 1),
(132, 'Anatomofisiología', 7, NULL, 1),
(133, 'Módulo Nº 1 Anatomía', 7, NULL, 1),
(134, 'Módulo Nº2 Fisiología', 7, NULL, 1),
(135, 'Química Biológica', 7, NULL, 1),
(136, 'Física Biológica', 7, NULL, 1),
(137, 'Introducción a la Enfermería en la Salud Pública', 7, NULL, 1),
(138, 'Introducción a las Ciencias Psicosociales', 7, NULL, 1),
(139, 'Enfermería Medica I', 7, NULL, 1),
(140, 'Deontología I', 7, NULL, 1),
(141, 'Microbiología y Parasitología', 7, NULL, 1),
(142, 'Nutrición', 7, NULL, 1),
(143, 'Enfermería En Salud Pública I', 7, NULL, 1),
(144, 'Enfermería en Salud Materno Infantil', 7, NULL, 1),
(145, 'Psicología Evolutiva', 7, NULL, 1),
(146, 'Enfermería en Salud Mental', 7, NULL, 1),
(147, 'Enfermería en Salud Pública II', 7, NULL, 1),
(148, 'Enfermería Médica II', 7, NULL, 1),
(149, 'Enfermería Quirúrgica', 7, NULL, 1),
(150, 'Dietoterapia', 7, NULL, 1),
(151, 'Enfermería Psiquiátrica', 7, NULL, 1),
(152, 'Deontología II', 7, NULL, 1),
(153, 'Enfermería Obstétrica', 7, NULL, 1),
(154, 'Enfermería Pediátrica', 7, NULL, 1),
(155, 'Introducción a la Administración en Enfermería', 7, NULL, 1),
(156, 'Introducción a la Salud Comunitaria', 8, NULL, 1),
(157, 'Anátomo-Fisiología I', 8, NULL, 1),
(158, 'Genética Humana', 8, NULL, 1),
(159, 'Introducción a la Obstetricia', 8, NULL, 1),
(160, 'Anátomo-Fisiología II', 8, NULL, 1),
(161, 'Salud Comunitaria I', 8, NULL, 1),
(162, 'Bioquímica', 8, NULL, 1),
(163, 'Cultura y alfabetización digital en la universidad', 8, NULL, 1),
(164, 'Introducción a la Nutrición', 8, NULL, 1),
(165, 'Salud Comunitaria II', 8, NULL, 1),
(166, 'Obstetricia I', 8, NULL, 1),
(167, 'Salud Sexual y Reproductiva', 8, NULL, 1),
(168, 'Obstetricia II', 8, NULL, 1),
(169, 'Antropología', 8, NULL, 1),
(170, 'Salud Comunitaria III', 8, NULL, 1),
(171, 'Asignatura UNAHUR', 8, NULL, 1),
(172, 'Psicología', 8, NULL, 1),
(173, 'Obstetricia III', 8, NULL, 1),
(174, 'Salud Comunitaria IV', 8, NULL, 1),
(175, 'Deontología y aspectos legales del ejercicio profe', 8, NULL, 1),
(176, 'Obstetricia patológica', 8, NULL, 1),
(177, 'Obstetricia IV', 8, NULL, 1),
(178, 'Preparación Integral para la maternidad', 8, NULL, 1),
(179, 'Microbiología', 8, NULL, 1),
(180, 'Farmacología', 8, NULL, 1),
(181, 'Evaluación de salud fetal', 8, NULL, 1),
(182, 'Farmacología Obstétrica', 8, NULL, 1),
(183, 'Práctica obstétrica integrada I', 8, NULL, 1),
(184, 'Taller de investigación I', 8, NULL, 1),
(185, 'Salud Comunitaria V', 8, NULL, 1),
(186, 'Historia Sociosanitaria de la Salud', 8, NULL, 1),
(187, 'Ética y desarrollo Profesional', 8, NULL, 1),
(188, 'Puericultura', 8, NULL, 1),
(189, 'Taller de investigación II', 8, NULL, 1),
(190, 'Práctica obstétrica integrada II', 8, NULL, 1),
(191, 'Introducción al Diseño y Modelado 3D', 9, NULL, 1),
(192, 'Modelado 3D (protrusión y revolución)', 9, NULL, 1),
(193, 'Aplicaciones de modelado 3D', 9, NULL, 1),
(194, 'Definición de propiedades de las piezas modeladas', 9, NULL, 1),
(195, 'Modelado de conjuntos. E', 9, NULL, 1),
(196, 'Aplicación de movimientos', 9, NULL, 1),
(197, 'Introducción a los sistemas de modelado en 3D', 9, NULL, 1),
(198, 'Impresoras de metales y moldes', 9, NULL, 1),
(199, 'Introducción a la Programación con Python', 10, NULL, 1),
(200, 'Estructuras de Datos', 10, NULL, 1),
(201, 'Sentencias de control de flujo y funciones', 10, NULL, 1),
(202, 'Programación orientada a objetos', 10, NULL, 1),
(203, 'Análisis exploratorio, curación y visualización de', 10, NULL, 1),
(204, 'Análisis exploratorio, curación y visualización de', 10, NULL, 1),
(205, 'Aprendizaje automático, Regresión', 10, NULL, 1),
(206, 'Aprendizaje automático, Clasificación', 10, NULL, 1),
(207, 'Árboles de decisión', 10, NULL, 1),
(208, 'Support Vector Machines', 10, NULL, 1),
(209, 'Redes neuronales I', 10, NULL, 1),
(210, 'Redes neuronales II', 10, NULL, 1),
(211, 'Introducción al Desarrollo Frontend con React', 11, NULL, 1),
(212, 'Introducción al Desarrollo Backend con Nodejs', 11, NULL, 1),
(213, 'Diseño UX', 11, NULL, 1),
(214, 'Proyecto Final de Desarrollo Web', 11, NULL, 1),
(215, 'Lectura y Escritura Académica', NULL, 3, 1),
(216, 'Matemática', NULL, 3, 1),
(217, 'Introducción al Conocimiento de la Física y la Química', NULL, 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id_profesor` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(20) NOT NULL,
  `correo` varchar(40) NOT NULL,
  `telefono` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id_profesor`, `nombre`, `apellido`, `correo`, `telefono`) VALUES
(1, 'Cudi', 'Ejemplo', 'correo@gmail.com', 1122222222);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `universidades`
--

CREATE TABLE `universidades` (
  `id_universidad` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `acronimo` varchar(6) NOT NULL,
  `carrera_id` int(11) NOT NULL,
  `curso_pre_admision_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `universidades`
--

INSERT INTO `universidades` (`id_universidad`, `nombre`, `acronimo`, `carrera_id`, `curso_pre_admision_id`) VALUES
(1, 'Universidad Nacional de Quilmes', 'UNQUI', 3, 3);

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
  ADD PRIMARY KEY (`id_carrera`);

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
  ADD KEY `aula_id` (`aula_id`);

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
  ADD KEY `carrera_id` (`carrera_id`),
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
  MODIFY `id_aula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `carreras`
--
ALTER TABLE `carreras`
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `cursos_pre_admisiones`
--
ALTER TABLE `cursos_pre_admisiones`
  MODIFY `id_curso_pre_admision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `dias`
--
ALTER TABLE `dias`
  MODIFY `id_dia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `itinerario`
--
ALTER TABLE `itinerario`
  MODIFY `id_itinerario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `jornada`
--
ALTER TABLE `jornada`
  MODIFY `id_jornada` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `universidades`
--
ALTER TABLE `universidades`
  MODIFY `id_universidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `dias`
--
ALTER TABLE `dias`
  ADD CONSTRAINT `dias_ibfk_1` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id_aula`),
  ADD CONSTRAINT `dias_ibfk_2` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id_materia`),
  ADD CONSTRAINT `dias_ibfk_3` FOREIGN KEY (`itinerario_id`) REFERENCES `itinerario` (`id_itinerario`),
  ADD CONSTRAINT `dias_ibfk_4` FOREIGN KEY (`jornada_id`) REFERENCES `jornada` (`id_jornada`);

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id_profesor`),
  ADD CONSTRAINT `materias_ibfk_2` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `materias_ibfk_3` FOREIGN KEY (`curso_pre_admision_id`) REFERENCES `cursos_pre_admisiones` (`id_curso_pre_admision`);

--
-- Filtros para la tabla `universidades`
--
ALTER TABLE `universidades`
  ADD CONSTRAINT `universidades_ibfk_1` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id_carrera`),
  ADD CONSTRAINT `universidades_ibfk_2` FOREIGN KEY (`curso_pre_admision_id`) REFERENCES `cursos_pre_admisiones` (`id_curso_pre_admision`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
