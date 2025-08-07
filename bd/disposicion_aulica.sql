-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-08-2025 a las 03:31:52
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
(8, 'Licenciatura en Obstetricia', 3);

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
-- Estructura de tabla para la tabla `itinerario`
--

CREATE TABLE `itinerario` (
  `id_itinerario` int(11) NOT NULL,
  `hora_fin` time NOT NULL,
  `hora_inicio` time NOT NULL,
  `turno_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `itinerario`
--

INSERT INTO `itinerario` (`id_itinerario`, `hora_fin`, `hora_inicio`, `turno_id`) VALUES
(1, '11:00:00', '09:00:00', 1),
(2, '12:45:00', '11:00:00', 1),
(3, '15:30:00', '13:30:00', 2),
(4, '17:45:00', '15:30:00', 2),
(5, '20:00:00', '18:30:00', 3),
(6, '22:30:00', '20:00:00', 3);

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
(1, 'Inglés Básico', 3, NULL, 2),
(2, 'Inglés Técnico', 3, NULL, 2),
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
(36, 'Asignatura UNAHUR', 2, NULL, 1),
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
(228, 'Introducción al Conocimiento de la Sociedad y el Estado (ICSE)', NULL, 6, NULL),
(229, 'Inglés Básico', 3, NULL, 2),
(230, 'Inglés Técnico', 3, NULL, 2),
(231, 'Informática', 3, NULL, 2),
(232, 'Técnicas Básicas de Laboratorio', 3, NULL, NULL),
(233, 'Matemática Aplicada', 3, NULL, NULL),
(234, 'Química General', 3, NULL, NULL),
(235, 'Química Orgánica', 3, NULL, NULL),
(236, 'Taller de Física Aplicada', 3, NULL, NULL),
(237, 'Fundamentos en Biología Celular y Molecular', 3, NULL, NULL),
(238, 'Biotecnología Clásica y Moderna', 3, NULL, NULL),
(239, 'Bioquímica', 3, NULL, NULL),
(240, 'Laboratorio de Química Instrumental', 3, NULL, NULL),
(241, 'Bases de la Microbiología Aplicada', 3, NULL, 2),
(242, 'Técnicas Inmunológicas', 3, NULL, NULL),
(243, 'Estadística Aplicada', 3, NULL, NULL),
(244, 'Higiene y Seguridad Industrial', 3, NULL, NULL),
(245, 'Técnicas de Biología Molecular y Genética', 3, NULL, NULL),
(246, 'Producción por Fermentadores', 3, NULL, NULL),
(247, 'Modelos Animales y Bioterio', 3, NULL, NULL),
(248, 'Bioinformática', 3, NULL, NULL),
(249, 'Introducción a la Biotecnología Animal', 3, NULL, NULL),
(250, 'Introducción a la Biotecnología Vegetal', 3, NULL, NULL),
(251, 'Buenas Prácticas de Laboratorio', 3, NULL, NULL),
(252, 'Buenas Prácticas en la Producción Farmacéutica', 3, NULL, NULL),
(253, 'Facturación y Ventas', 3, NULL, NULL),
(254, 'Curso Complementario', 3, NULL, NULL),
(255, 'Introducción a la Tecnología de los Alimentos', 2, NULL, NULL),
(256, 'Matemática I', 2, NULL, NULL),
(257, 'Nuevos Entornos y Lenguajes: la producción del con', 2, NULL, NULL),
(258, 'Biología General', 2, NULL, NULL),
(259, 'Introducción a la Química', 2, NULL, NULL),
(260, 'Higiene y Seguridad', 2, NULL, NULL),
(261, 'Química General e Inorgánica', 2, NULL, NULL),
(262, 'Matemática II', 2, NULL, NULL),
(263, 'Inglés I', 2, NULL, NULL),
(264, 'Asignatura UNAHUR', 2, NULL, NULL),
(265, 'Introducción al Laboratorio de Análisis de Aliment', 2, NULL, NULL),
(266, 'Microbiología General', 2, NULL, NULL),
(267, 'Física', 2, NULL, NULL),
(268, 'Química Orgánica', 2, NULL, NULL),
(269, 'Microbiología de los Alimentos I', 2, NULL, NULL),
(270, 'Fisicoquímica de los Alimentos I', 2, NULL, NULL),
(271, 'Química de los Alimentos', 2, NULL, NULL),
(272, 'Laboratorio de química Instrumental y Analítica', 2, NULL, NULL),
(273, 'Taller de Bromatología y Análisis de la Calidad', 2, NULL, NULL),
(274, 'Gestión de la Calidad e Inocuidad de los Alimentos', 2, NULL, NULL),
(275, 'Operaciones Unitarias I', 2, NULL, NULL),
(276, 'Seminario General de Procesos Productivos de los A', 2, NULL, NULL),
(277, 'Introducción al Diseño', 4, NULL, NULL),
(278, 'Sistemas de representación gráfica', 4, NULL, NULL),
(279, 'Tecnología I', 4, NULL, NULL),
(280, 'Modelado', 4, NULL, NULL),
(281, 'Taller de Diseño I', 4, NULL, NULL),
(282, 'Matemática', 4, NULL, NULL),
(283, 'Morfología I', 4, NULL, NULL),
(284, 'Nuevos entornos y lenguajes: la producción del con', 4, NULL, NULL),
(285, 'Taller de Diseño II', 4, NULL, NULL),
(286, 'Tecnología y sociedad', 4, NULL, NULL),
(287, 'Taller de producción I', 4, NULL, NULL),
(288, 'Tecnología II', 4, NULL, NULL),
(289, 'Programación', 4, NULL, NULL),
(290, 'Ciencias aplicadas al diseño', 4, NULL, NULL),
(291, 'Taller de Diseño III', 4, NULL, NULL),
(292, 'Morfología II', 4, NULL, NULL),
(293, 'Tecnologías de fabricación digital I', 4, NULL, NULL),
(294, 'Asignatura UNAHUR', 4, NULL, NULL),
(295, 'Taller de Diseño IV', 4, NULL, NULL),
(296, 'Tecnología III', 4, NULL, NULL),
(297, 'Tecnologías de fabricación digital II', 4, NULL, NULL),
(298, 'Diseño e industria', 4, NULL, NULL),
(299, 'Inglés I', 4, NULL, NULL),
(300, 'Programación I', 5, NULL, NULL),
(301, 'Sistemas de Procesamientos de datos', 5, NULL, NULL),
(302, 'Matemática', 5, NULL, NULL),
(303, 'Ingles I', 5, NULL, NULL),
(304, 'Laboratorio de Computación I', 5, NULL, NULL),
(305, 'Programación II', 5, NULL, NULL),
(306, 'Arquitectura y Sistemas Operativos', 5, NULL, NULL),
(307, 'Estadística', 5, NULL, NULL),
(308, 'Metodología de la Investigación', 5, NULL, NULL),
(309, 'Inglés II', 5, NULL, NULL),
(310, 'Laboratorio de Computación II', 5, NULL, NULL),
(311, 'Programación III', 5, NULL, NULL),
(312, 'Organización Contable de la Empresa', 5, NULL, NULL),
(313, 'Organización Empresarial', 5, NULL, NULL),
(314, 'Elementos de Investigación Operativa', 5, NULL, NULL),
(315, 'Laboratorio de Computación III', 5, NULL, NULL),
(316, 'Diseño y administración de bases de datos', 5, NULL, NULL),
(317, 'Metodología de Sistemas I', 5, NULL, NULL),
(318, 'Legislación', 5, NULL, NULL),
(319, 'Laboratorio de Computación IV', 5, NULL, NULL),
(320, 'Práctica Profesional', 5, NULL, NULL),
(321, 'Economía de la cultura', 6, NULL, NULL),
(322, 'Cultura lúdica: jugar es humano', 6, NULL, NULL),
(323, 'Inglés II', 6, NULL, NULL),
(324, 'Planificación de negocios', 6, NULL, NULL),
(325, 'Metodología de la investigación II', 6, NULL, NULL),
(326, 'Inglés I', 6, NULL, NULL),
(327, 'Ética y liderazgo', 6, NULL, NULL),
(328, 'Taller proyectual', 6, NULL, NULL),
(329, 'Metodología de la investigación I', 6, NULL, NULL),
(330, 'Taller introductorio al diseño en 3D', 6, NULL, NULL),
(331, 'Q.A. (\"Control de calidad\")', 6, NULL, NULL),
(332, 'Producción y prácticas lúdicas II', 6, NULL, NULL),
(333, 'Internacionalización de proyectos', 6, NULL, NULL),
(334, 'Marketing digital', 6, NULL, NULL),
(335, 'Narrativas transmedia', 6, NULL, NULL),
(336, 'Taller de desarrollo de entornos virtuales', 6, NULL, NULL),
(337, 'Juegos serios II', 6, NULL, NULL),
(338, 'Modelos organizacionales', 6, NULL, NULL),
(339, 'Diseño lúdico II', 6, NULL, NULL),
(340, 'Taller de prototipado digital', 6, NULL, NULL),
(341, 'Taller de diseño y animación en 2D', 6, NULL, NULL),
(342, 'Juegos serios I', 6, NULL, NULL),
(343, 'Industria del videojuego', 6, NULL, NULL),
(344, 'Taller de diseño UIX/GUI', 6, NULL, NULL),
(345, 'Aspectos legales del desarrollo de videojuegos', 6, NULL, NULL),
(346, 'Producción y prácticas lúdicas I', 6, NULL, NULL),
(347, 'Historia de los videojuegos', 6, NULL, NULL),
(348, 'Gestión de proyectos', 6, NULL, NULL),
(349, 'Historia de la cultura II', 6, NULL, NULL),
(350, 'Historia del cine', 6, NULL, NULL),
(351, 'Pensamiento social argentino y latinoamericano', 6, NULL, NULL),
(352, 'Fundamentos de la programación I', 6, NULL, NULL),
(353, 'Diseño lúdico I', 6, NULL, NULL),
(354, 'Fundamentos de la programación II', 6, NULL, NULL),
(355, 'Historia de la cultura I', 6, NULL, NULL),
(356, 'La tecnología y sus usos', 6, NULL, NULL),
(357, 'Literatura y pensamiento', 6, NULL, NULL),
(358, 'Introducción al medio audiovisual', 6, NULL, NULL),
(359, 'Introducción a la comunicación', 6, NULL, NULL),
(360, 'Anatomofisiología', 7, NULL, NULL),
(361, 'Módulo Nº 1 Anatomía', 7, NULL, NULL),
(362, 'Módulo Nº2 Fisiología', 7, NULL, NULL),
(363, 'Química Biológica', 7, NULL, NULL),
(364, 'Física Biológica', 7, NULL, NULL),
(365, 'Introducción a la Enfermería en la Salud Pública', 7, NULL, NULL),
(366, 'Introducción a las Ciencias Psicosociales', 7, NULL, NULL),
(367, 'Enfermería Medica I', 7, NULL, NULL),
(368, 'Deontología I', 7, NULL, NULL),
(369, 'Microbiología y Parasitología', 7, NULL, NULL),
(370, 'Nutrición', 7, NULL, NULL),
(371, 'Enfermería En Salud Pública I', 7, NULL, NULL),
(372, 'Enfermería en Salud Materno Infantil', 7, NULL, NULL),
(373, 'Psicología Evolutiva', 7, NULL, NULL),
(374, 'Enfermería en Salud Mental', 7, NULL, NULL),
(375, 'Enfermería en Salud Pública II', 7, NULL, NULL),
(376, 'Enfermería Médica II', 7, NULL, NULL),
(377, 'Enfermería Quirúrgica', 7, NULL, NULL),
(378, 'Dietoterapia', 7, NULL, NULL),
(379, 'Enfermería Psiquiátrica', 7, NULL, NULL),
(380, 'Deontología II', 7, NULL, NULL),
(381, 'Enfermería Obstétrica', 7, NULL, NULL),
(382, 'Enfermería Pediátrica', 7, NULL, NULL),
(383, 'Introducción a la Administración en Enfermería', 7, NULL, NULL),
(384, 'Introducción a la Salud Comunitaria', 8, NULL, NULL),
(385, 'Anátomo-Fisiología I', 8, NULL, NULL),
(386, 'Genética Humana', 8, NULL, NULL),
(387, 'Introducción a la Obstetricia', 8, NULL, NULL),
(388, 'Anátomo-Fisiología II', 8, NULL, NULL),
(389, 'Salud Comunitaria I', 8, NULL, NULL),
(390, 'Bioquímica', 8, NULL, NULL),
(391, 'Cultura y alfabetización digital en la universidad', 8, NULL, NULL),
(392, 'Introducción a la Nutrición', 8, NULL, NULL),
(393, 'Salud Comunitaria II', 8, NULL, NULL),
(394, 'Obstetricia I', 8, NULL, NULL),
(395, 'Salud Sexual y Reproductiva', 8, NULL, NULL),
(396, 'Obstetricia II', 8, NULL, NULL),
(397, 'Antropología', 8, NULL, NULL),
(398, 'Salud Comunitaria III', 8, NULL, NULL),
(399, 'Asignatura UNAHUR', 8, NULL, NULL),
(400, 'Psicología', 8, NULL, NULL),
(401, 'Obstetricia III', 8, NULL, NULL),
(402, 'Salud Comunitaria IV', 8, NULL, NULL),
(403, 'Deontología y aspectos legales del Ejercicio Profesional', 8, NULL, NULL),
(404, 'Obstetricia patológica', 8, NULL, NULL),
(405, 'Obstetricia IV', 8, NULL, NULL),
(406, 'Preparación Integral para la maternidad', 8, NULL, NULL),
(407, 'Microbiología', 8, NULL, NULL),
(408, 'Farmacología', 8, NULL, NULL),
(409, 'Evaluación de salud fetal', 8, NULL, NULL),
(410, 'Farmacología Obstétrica', 8, NULL, NULL),
(411, 'Práctica obstétrica integrada I', 8, NULL, NULL),
(412, 'Taller de investigación I', 8, NULL, NULL),
(413, 'Salud Comunitaria V', 8, NULL, NULL),
(414, 'Historia Sociosanitaria de la Salud', 8, NULL, NULL),
(415, 'Ética y desarrollo Profesional', 8, NULL, NULL),
(416, 'Puericultura', 8, NULL, NULL),
(417, 'Taller de investigación II', 8, NULL, NULL),
(418, 'Práctica obstétrica integrada II', 8, NULL, NULL),
(419, 'Lectura y Escritura Académica', NULL, 3, NULL),
(420, 'Matemática', NULL, 3, NULL),
(421, 'Introducción al Conocimiento de la Física y la Química', NULL, 3, NULL),
(422, 'Pensamiento Matemático', NULL, 4, NULL),
(423, 'Lectura y Escritura', NULL, 4, NULL),
(424, 'Vida Universitaria', NULL, 4, NULL),
(425, 'Programación Inicial', NULL, 7, NULL),
(426, 'Matemática Inicial', NULL, 7, NULL),
(427, 'Lectura Comprensiva', NULL, 7, NULL),
(428, 'Matemática ', NULL, 5, NULL),
(429, 'Lectura y Escritura', NULL, 5, NULL),
(430, 'Sociedad y Vida universitaria', NULL, 5, NULL),
(431, 'Introducción al Pensamiento Científico (IPC)', NULL, 6, NULL),
(432, 'Introducción al Conocimiento de la Sociedad y el Estado (ICSE)', NULL, 6, NULL),
(433, 'Inglés Básico', 3, NULL, NULL),
(434, 'Inglés Técnico', 3, NULL, 2),
(435, 'Informática', 3, NULL, 2),
(436, 'Técnicas Básicas de Laboratorio', 3, NULL, NULL),
(437, 'Matemática Aplicada', 3, NULL, NULL),
(438, 'Química General', 3, NULL, NULL),
(439, 'Química Orgánica', 3, NULL, NULL),
(440, 'Taller de Física Aplicada', 3, NULL, NULL),
(441, 'Fundamentos en Biología Celular y Molecular', 3, NULL, NULL),
(442, 'Biotecnología Clásica y Moderna', 3, NULL, NULL),
(443, 'Bioquímica', 3, NULL, NULL),
(444, 'Laboratorio de Química Instrumental', 3, NULL, NULL),
(445, 'Bases de la Microbiología Aplicada', 3, NULL, 2),
(446, 'Técnicas Inmunológicas', 3, NULL, NULL),
(447, 'Estadística Aplicada', 3, NULL, NULL),
(448, 'Higiene y Seguridad Industrial', 3, NULL, NULL),
(449, 'Técnicas de Biología Molecular y Genética', 3, NULL, NULL),
(450, 'Producción por Fermentadores', 3, NULL, NULL),
(451, 'Modelos Animales y Bioterio', 3, NULL, NULL),
(452, 'Bioinformática', 3, NULL, NULL),
(453, 'Introducción a la Biotecnología Animal', 3, NULL, NULL),
(454, 'Introducción a la Biotecnología Vegetal', 3, NULL, NULL),
(455, 'Buenas Prácticas de Laboratorio', 3, NULL, NULL),
(456, 'Buenas Prácticas en la Producción Farmacéutica', 3, NULL, NULL),
(457, 'Facturación y Ventas', 3, NULL, NULL),
(458, 'Curso Complementario', 3, NULL, NULL),
(459, 'Introducción a la Tecnología de los Alimentos', 2, NULL, NULL),
(460, 'Matemática I', 2, NULL, NULL),
(461, 'Nuevos Entornos y Lenguajes: la producción del con', 2, NULL, NULL),
(462, 'Biología General', 2, NULL, NULL),
(463, 'Introducción a la Química', 2, NULL, NULL),
(464, 'Higiene y Seguridad', 2, NULL, NULL),
(465, 'Química General e Inorgánica', 2, NULL, NULL),
(466, 'Matemática II', 2, NULL, NULL),
(467, 'Inglés I', 2, NULL, NULL),
(468, 'Asignatura UNAHUR', 2, NULL, NULL),
(469, 'Introducción al Laboratorio de Análisis de Aliment', 2, NULL, NULL),
(470, 'Microbiología General', 2, NULL, NULL),
(471, 'Física', 2, NULL, NULL),
(472, 'Química Orgánica', 2, NULL, NULL),
(473, 'Microbiología de los Alimentos I', 2, NULL, NULL),
(474, 'Fisicoquímica de los Alimentos I', 2, NULL, NULL),
(475, 'Química de los Alimentos', 2, NULL, NULL),
(476, 'Laboratorio de química Instrumental y Analítica', 2, NULL, NULL),
(477, 'Taller de Bromatología y Análisis de la Calidad', 2, NULL, NULL),
(478, 'Gestión de la Calidad e Inocuidad de los Alimentos', 2, NULL, NULL),
(479, 'Operaciones Unitarias I', 2, NULL, NULL),
(480, 'Seminario General de Procesos Productivos de los A', 2, NULL, NULL),
(481, 'Introducción al Diseño', 4, NULL, NULL),
(482, 'Sistemas de representación gráfica', 4, NULL, NULL),
(483, 'Tecnología I', 4, NULL, NULL),
(484, 'Modelado', 4, NULL, NULL),
(485, 'Taller de Diseño I', 4, NULL, NULL),
(486, 'Matemática', 4, NULL, NULL),
(487, 'Morfología I', 4, NULL, NULL),
(488, 'Nuevos entornos y lenguajes: la producción del con', 4, NULL, NULL),
(489, 'Taller de Diseño II', 4, NULL, NULL),
(490, 'Tecnología y sociedad', 4, NULL, NULL),
(491, 'Taller de producción I', 4, NULL, NULL),
(492, 'Tecnología II', 4, NULL, NULL),
(493, 'Programación', 4, NULL, NULL),
(494, 'Ciencias aplicadas al diseño', 4, NULL, NULL),
(495, 'Taller de Diseño III', 4, NULL, NULL),
(496, 'Morfología II', 4, NULL, NULL),
(497, 'Tecnologías de fabricación digital I', 4, NULL, NULL),
(498, 'Asignatura UNAHUR', 4, NULL, NULL),
(499, 'Taller de Diseño IV', 4, NULL, NULL),
(500, 'Tecnología III', 4, NULL, NULL),
(501, 'Tecnologías de fabricación digital II', 4, NULL, NULL),
(502, 'Diseño e industria', 4, NULL, NULL),
(503, 'Inglés I', 4, NULL, NULL),
(504, 'Programación I', 5, NULL, NULL),
(505, 'Sistemas de Procesamientos de datos', 5, NULL, NULL),
(506, 'Matemática', 5, NULL, NULL),
(507, 'Ingles I', 5, NULL, NULL),
(508, 'Laboratorio de Computación I', 5, NULL, NULL),
(509, 'Programación II', 5, NULL, NULL),
(510, 'Arquitectura y Sistemas Operativos', 5, NULL, NULL),
(511, 'Estadística', 5, NULL, NULL),
(512, 'Metodología de la Investigación', 5, NULL, NULL),
(513, 'Inglés II', 5, NULL, NULL),
(514, 'Laboratorio de Computación II', 5, NULL, NULL),
(515, 'Programación III', 5, NULL, NULL),
(516, 'Organización Contable de la Empresa', 5, NULL, NULL),
(517, 'Organización Empresarial', 5, NULL, NULL),
(518, 'Elementos de Investigación Operativa', 5, NULL, NULL),
(519, 'Laboratorio de Computación III', 5, NULL, NULL),
(520, 'Diseño y administración de bases de datos', 5, NULL, NULL),
(521, 'Metodología de Sistemas I', 5, NULL, NULL),
(522, 'Legislación', 5, NULL, NULL),
(523, 'Laboratorio de Computación IV', 5, NULL, NULL),
(524, 'Práctica Profesional', 5, NULL, NULL),
(525, 'Economía de la cultura', 6, NULL, NULL),
(526, 'Cultura lúdica: jugar es humano', 6, NULL, NULL),
(527, 'Inglés II', 6, NULL, NULL),
(528, 'Planificación de negocios', 6, NULL, NULL),
(529, 'Metodología de la investigación II', 6, NULL, NULL),
(530, 'Inglés I', 6, NULL, NULL),
(531, 'Ética y liderazgo', 6, NULL, NULL),
(532, 'Taller proyectual', 6, NULL, NULL),
(533, 'Metodología de la investigación I', 6, NULL, NULL),
(534, 'Taller introductorio al diseño en 3D', 6, NULL, NULL),
(535, 'Q.A. (\"Control de calidad\")', 6, NULL, NULL),
(536, 'Producción y prácticas lúdicas II', 6, NULL, NULL),
(537, 'Internacionalización de proyectos', 6, NULL, NULL),
(538, 'Marketing digital', 6, NULL, NULL),
(539, 'Narrativas transmedia', 6, NULL, NULL),
(540, 'Taller de desarrollo de entornos virtuales', 6, NULL, NULL),
(541, 'Juegos serios II', 6, NULL, NULL),
(542, 'Modelos organizacionales', 6, NULL, NULL),
(543, 'Diseño lúdico II', 6, NULL, NULL),
(544, 'Taller de prototipado digital', 6, NULL, NULL),
(545, 'Taller de diseño y animación en 2D', 6, NULL, NULL),
(546, 'Juegos serios I', 6, NULL, NULL),
(547, 'Industria del videojuego', 6, NULL, NULL),
(548, 'Taller de diseño UIX/GUI', 6, NULL, NULL),
(549, 'Aspectos legales del desarrollo de videojuegos', 6, NULL, NULL),
(550, 'Producción y prácticas lúdicas I', 6, NULL, NULL),
(551, 'Historia de los videojuegos', 6, NULL, NULL),
(552, 'Gestión de proyectos', 6, NULL, NULL),
(553, 'Historia de la cultura II', 6, NULL, NULL),
(554, 'Historia del cine', 6, NULL, NULL),
(555, 'Pensamiento social argentino y latinoamericano', 6, NULL, NULL),
(556, 'Fundamentos de la programación I', 6, NULL, NULL),
(557, 'Diseño lúdico I', 6, NULL, NULL),
(558, 'Fundamentos de la programación II', 6, NULL, NULL),
(559, 'Historia de la cultura I', 6, NULL, NULL),
(560, 'La tecnología y sus usos', 6, NULL, NULL),
(561, 'Literatura y pensamiento', 6, NULL, NULL),
(562, 'Introducción al medio audiovisual', 6, NULL, NULL),
(563, 'Introducción a la comunicación', 6, NULL, NULL),
(564, 'Anatomofisiología', 7, NULL, NULL),
(565, 'Módulo Nº 1 Anatomía', 7, NULL, NULL),
(566, 'Módulo Nº2 Fisiología', 7, NULL, NULL),
(567, 'Química Biológica', 7, NULL, NULL),
(568, 'Física Biológica', 7, NULL, NULL),
(569, 'Introducción a la Enfermería en la Salud Pública', 7, NULL, NULL),
(570, 'Introducción a las Ciencias Psicosociales', 7, NULL, NULL),
(571, 'Enfermería Medica I', 7, NULL, NULL),
(572, 'Deontología I', 7, NULL, NULL),
(573, 'Microbiología y Parasitología', 7, NULL, NULL),
(574, 'Nutrición', 7, NULL, NULL),
(575, 'Enfermería En Salud Pública I', 7, NULL, NULL),
(576, 'Enfermería en Salud Materno Infantil', 7, NULL, NULL),
(577, 'Psicología Evolutiva', 7, NULL, NULL),
(578, 'Enfermería en Salud Mental', 7, NULL, NULL),
(579, 'Enfermería en Salud Pública II', 7, NULL, NULL),
(580, 'Enfermería Médica II', 7, NULL, NULL),
(581, 'Enfermería Quirúrgica', 7, NULL, NULL),
(582, 'Dietoterapia', 7, NULL, NULL),
(583, 'Enfermería Psiquiátrica', 7, NULL, NULL),
(584, 'Deontología II', 7, NULL, NULL),
(585, 'Enfermería Obstétrica', 7, NULL, NULL),
(586, 'Enfermería Pediátrica', 7, NULL, NULL),
(587, 'Introducción a la Administración en Enfermería', 7, NULL, NULL),
(588, 'Introducción a la Salud Comunitaria', 8, NULL, NULL),
(589, 'Anátomo-Fisiología I', 8, NULL, NULL),
(590, 'Genética Humana', 8, NULL, NULL),
(591, 'Introducción a la Obstetricia', 8, NULL, NULL),
(592, 'Anátomo-Fisiología II', 8, NULL, NULL),
(593, 'Salud Comunitaria I', 8, NULL, NULL),
(594, 'Bioquímica', 8, NULL, NULL),
(595, 'Cultura y alfabetización digital en la universidad', 8, NULL, NULL),
(596, 'Introducción a la Nutrición', 8, NULL, NULL),
(597, 'Salud Comunitaria II', 8, NULL, NULL),
(598, 'Obstetricia I', 8, NULL, NULL),
(599, 'Salud Sexual y Reproductiva', 8, NULL, NULL),
(600, 'Obstetricia II', 8, NULL, NULL),
(601, 'Antropología', 8, NULL, NULL),
(602, 'Salud Comunitaria III', 8, NULL, NULL),
(603, 'Asignatura UNAHUR', 8, NULL, NULL),
(604, 'Psicología', 8, NULL, NULL),
(605, 'Obstetricia III', 8, NULL, NULL),
(606, 'Salud Comunitaria IV', 8, NULL, NULL),
(607, 'Deontología y aspectos legales del Ejercicio Profesional', 8, NULL, NULL),
(608, 'Obstetricia patológica', 8, NULL, NULL),
(609, 'Obstetricia IV', 8, NULL, NULL),
(610, 'Preparación Integral para la maternidad', 8, NULL, NULL),
(611, 'Microbiología', 8, NULL, NULL),
(612, 'Farmacología', 8, NULL, NULL),
(613, 'Evaluación de salud fetal', 8, NULL, NULL),
(614, 'Farmacología Obstétrica', 8, NULL, NULL),
(615, 'Práctica obstétrica integrada I', 8, NULL, NULL),
(616, 'Taller de investigación I', 8, NULL, NULL),
(617, 'Salud Comunitaria V', 8, NULL, NULL),
(618, 'Historia Sociosanitaria de la Salud', 8, NULL, NULL),
(619, 'Ética y desarrollo Profesional', 8, NULL, NULL),
(620, 'Puericultura', 8, NULL, NULL),
(621, 'Taller de investigación II', 8, NULL, NULL),
(622, 'Práctica obstétrica integrada II', 8, NULL, NULL),
(623, 'Lectura y Escritura Académica', NULL, 3, NULL),
(624, 'Matemática', NULL, 3, NULL),
(625, 'Introducción al Conocimiento de la Física y la Química', NULL, 3, NULL),
(626, 'Pensamiento Matemático', NULL, 4, NULL),
(627, 'Lectura y Escritura', NULL, 4, NULL),
(628, 'Vida Universitaria', NULL, 4, NULL),
(629, 'Programación Inicial', NULL, 7, NULL),
(630, 'Matemática Inicial', NULL, 7, NULL),
(631, 'Lectura Comprensiva', NULL, 7, NULL),
(632, 'Matemática ', NULL, 5, NULL),
(633, 'Lectura y Escritura', NULL, 5, NULL),
(634, 'Sociedad y Vida universitaria', NULL, 5, NULL),
(635, 'Introducción al Pensamiento Científico (IPC)', NULL, 6, NULL),
(636, 'Introducción al Conocimiento de la Sociedad y el Estado (ICSE)', NULL, 6, NULL),
(637, 'Inglés Básico', 3, NULL, NULL),
(638, 'Inglés Técnico', 3, NULL, 2),
(639, 'Informática', 3, NULL, 2),
(640, 'Técnicas Básicas de Laboratorio', 3, NULL, NULL),
(641, 'Matemática Aplicada', 3, NULL, NULL),
(642, 'Química General', 3, NULL, NULL),
(643, 'Química Orgánica', 3, NULL, NULL),
(644, 'Taller de Física Aplicada', 3, NULL, NULL),
(645, 'Fundamentos en Biología Celular y Molecular', 3, NULL, NULL),
(646, 'Biotecnología Clásica y Moderna', 3, NULL, NULL),
(647, 'Bioquímica', 3, NULL, NULL),
(648, 'Laboratorio de Química Instrumental', 3, NULL, NULL),
(649, 'Bases de la Microbiología Aplicada', 3, NULL, 2),
(650, 'Técnicas Inmunológicas', 3, NULL, NULL),
(651, 'Estadística Aplicada', 3, NULL, NULL),
(652, 'Higiene y Seguridad Industrial', 3, NULL, NULL),
(653, 'Técnicas de Biología Molecular y Genética', 3, NULL, NULL),
(654, 'Producción por Fermentadores', 3, NULL, NULL),
(655, 'Modelos Animales y Bioterio', 3, NULL, NULL),
(656, 'Bioinformática', 3, NULL, NULL),
(657, 'Introducción a la Biotecnología Animal', 3, NULL, NULL),
(658, 'Introducción a la Biotecnología Vegetal', 3, NULL, NULL),
(659, 'Buenas Prácticas de Laboratorio', 3, NULL, NULL),
(660, 'Buenas Prácticas en la Producción Farmacéutica', 3, NULL, NULL),
(661, 'Facturación y Ventas', 3, NULL, NULL),
(662, 'Curso Complementario', 3, NULL, NULL),
(663, 'Introducción a la Tecnología de los Alimentos', 2, NULL, NULL),
(664, 'Matemática I', 2, NULL, NULL),
(665, 'Nuevos Entornos y Lenguajes: la producción del con', 2, NULL, NULL),
(666, 'Biología General', 2, NULL, NULL),
(667, 'Introducción a la Química', 2, NULL, NULL),
(668, 'Higiene y Seguridad', 2, NULL, NULL),
(669, 'Química General e Inorgánica', 2, NULL, NULL),
(670, 'Matemática II', 2, NULL, NULL),
(671, 'Inglés I', 2, NULL, NULL),
(672, 'Asignatura UNAHUR', 2, NULL, NULL),
(673, 'Introducción al Laboratorio de Análisis de Aliment', 2, NULL, NULL),
(674, 'Microbiología General', 2, NULL, NULL),
(675, 'Física', 2, NULL, NULL),
(676, 'Química Orgánica', 2, NULL, NULL),
(677, 'Microbiología de los Alimentos I', 2, NULL, NULL),
(678, 'Fisicoquímica de los Alimentos I', 2, NULL, NULL),
(679, 'Química de los Alimentos', 2, NULL, NULL),
(680, 'Laboratorio de química Instrumental y Analítica', 2, NULL, NULL),
(681, 'Taller de Bromatología y Análisis de la Calidad', 2, NULL, NULL),
(682, 'Gestión de la Calidad e Inocuidad de los Alimentos', 2, NULL, NULL),
(683, 'Operaciones Unitarias I', 2, NULL, NULL),
(684, 'Seminario General de Procesos Productivos de los A', 2, NULL, NULL),
(685, 'Introducción al Diseño', 4, NULL, NULL),
(686, 'Sistemas de representación gráfica', 4, NULL, NULL),
(687, 'Tecnología I', 4, NULL, NULL),
(688, 'Modelado', 4, NULL, NULL),
(689, 'Taller de Diseño I', 4, NULL, NULL),
(690, 'Matemática', 4, NULL, NULL),
(691, 'Morfología I', 4, NULL, NULL),
(692, 'Nuevos entornos y lenguajes: la producción del con', 4, NULL, NULL),
(693, 'Taller de Diseño II', 4, NULL, NULL),
(694, 'Tecnología y sociedad', 4, NULL, NULL),
(695, 'Taller de producción I', 4, NULL, NULL),
(696, 'Tecnología II', 4, NULL, NULL),
(697, 'Programación', 4, NULL, NULL),
(698, 'Ciencias aplicadas al diseño', 4, NULL, NULL),
(699, 'Taller de Diseño III', 4, NULL, NULL),
(700, 'Morfología II', 4, NULL, NULL),
(701, 'Tecnologías de fabricación digital I', 4, NULL, NULL),
(702, 'Asignatura UNAHUR', 4, NULL, NULL),
(703, 'Taller de Diseño IV', 4, NULL, NULL),
(704, 'Tecnología III', 4, NULL, NULL),
(705, 'Tecnologías de fabricación digital II', 4, NULL, NULL),
(706, 'Diseño e industria', 4, NULL, NULL),
(707, 'Inglés I', 4, NULL, NULL),
(708, 'Programación I', 5, NULL, NULL),
(709, 'Sistemas de Procesamientos de datos', 5, NULL, NULL),
(710, 'Matemática', 5, NULL, NULL),
(711, 'Ingles I', 5, NULL, NULL),
(712, 'Laboratorio de Computación I', 5, NULL, NULL),
(713, 'Programación II', 5, NULL, NULL),
(714, 'Arquitectura y Sistemas Operativos', 5, NULL, NULL),
(715, 'Estadística', 5, NULL, NULL),
(716, 'Metodología de la Investigación', 5, NULL, NULL),
(717, 'Inglés II', 5, NULL, NULL),
(718, 'Laboratorio de Computación II', 5, NULL, NULL),
(719, 'Programación III', 5, NULL, NULL),
(720, 'Organización Contable de la Empresa', 5, NULL, NULL),
(721, 'Organización Empresarial', 5, NULL, NULL),
(722, 'Elementos de Investigación Operativa', 5, NULL, NULL),
(723, 'Laboratorio de Computación III', 5, NULL, NULL),
(724, 'Diseño y administración de bases de datos', 5, NULL, NULL),
(725, 'Metodología de Sistemas I', 5, NULL, NULL),
(726, 'Legislación', 5, NULL, NULL),
(727, 'Laboratorio de Computación IV', 5, NULL, NULL),
(728, 'Práctica Profesional', 5, NULL, NULL),
(729, 'Economía de la cultura', 6, NULL, NULL),
(730, 'Cultura lúdica: jugar es humano', 6, NULL, NULL),
(731, 'Inglés II', 6, NULL, NULL),
(732, 'Planificación de negocios', 6, NULL, NULL),
(733, 'Metodología de la investigación II', 6, NULL, NULL),
(734, 'Inglés I', 6, NULL, NULL),
(735, 'Ética y liderazgo', 6, NULL, NULL),
(736, 'Taller proyectual', 6, NULL, NULL),
(737, 'Metodología de la investigación I', 6, NULL, NULL),
(738, 'Taller introductorio al diseño en 3D', 6, NULL, NULL),
(739, 'Q.A. (\"Control de calidad\")', 6, NULL, NULL),
(740, 'Producción y prácticas lúdicas II', 6, NULL, NULL),
(741, 'Internacionalización de proyectos', 6, NULL, NULL),
(742, 'Marketing digital', 6, NULL, NULL),
(743, 'Narrativas transmedia', 6, NULL, NULL),
(744, 'Taller de desarrollo de entornos virtuales', 6, NULL, NULL),
(745, 'Juegos serios II', 6, NULL, NULL),
(746, 'Modelos organizacionales', 6, NULL, NULL),
(747, 'Diseño lúdico II', 6, NULL, NULL),
(748, 'Taller de prototipado digital', 6, NULL, NULL),
(749, 'Taller de diseño y animación en 2D', 6, NULL, NULL),
(750, 'Juegos serios I', 6, NULL, NULL),
(751, 'Industria del videojuego', 6, NULL, NULL),
(752, 'Taller de diseño UIX/GUI', 6, NULL, NULL),
(753, 'Aspectos legales del desarrollo de videojuegos', 6, NULL, NULL),
(754, 'Producción y prácticas lúdicas I', 6, NULL, NULL),
(755, 'Historia de los videojuegos', 6, NULL, NULL),
(756, 'Gestión de proyectos', 6, NULL, NULL),
(757, 'Historia de la cultura II', 6, NULL, NULL),
(758, 'Historia del cine', 6, NULL, NULL),
(759, 'Pensamiento social argentino y latinoamericano', 6, NULL, NULL),
(760, 'Fundamentos de la programación I', 6, NULL, NULL),
(761, 'Diseño lúdico I', 6, NULL, NULL),
(762, 'Fundamentos de la programación II', 6, NULL, NULL),
(763, 'Historia de la cultura I', 6, NULL, NULL),
(764, 'La tecnología y sus usos', 6, NULL, NULL),
(765, 'Literatura y pensamiento', 6, NULL, NULL),
(766, 'Introducción al medio audiovisual', 6, NULL, NULL),
(767, 'Introducción a la comunicación', 6, NULL, NULL),
(768, 'Anatomofisiología', 7, NULL, NULL),
(769, 'Módulo Nº 1 Anatomía', 7, NULL, NULL),
(770, 'Módulo Nº2 Fisiología', 7, NULL, NULL),
(771, 'Química Biológica', 7, NULL, NULL),
(772, 'Física Biológica', 7, NULL, NULL),
(773, 'Introducción a la Enfermería en la Salud Pública', 7, NULL, NULL),
(774, 'Introducción a las Ciencias Psicosociales', 7, NULL, NULL),
(775, 'Enfermería Medica I', 7, NULL, NULL),
(776, 'Deontología I', 7, NULL, NULL),
(777, 'Microbiología y Parasitología', 7, NULL, NULL),
(778, 'Nutrición', 7, NULL, NULL),
(779, 'Enfermería En Salud Pública I', 7, NULL, NULL),
(780, 'Enfermería en Salud Materno Infantil', 7, NULL, NULL),
(781, 'Psicología Evolutiva', 7, NULL, NULL),
(782, 'Enfermería en Salud Mental', 7, NULL, NULL),
(783, 'Enfermería en Salud Pública II', 7, NULL, NULL),
(784, 'Enfermería Médica II', 7, NULL, NULL),
(785, 'Enfermería Quirúrgica', 7, NULL, NULL),
(786, 'Dietoterapia', 7, NULL, NULL),
(787, 'Enfermería Psiquiátrica', 7, NULL, NULL),
(788, 'Deontología II', 7, NULL, NULL),
(789, 'Enfermería Obstétrica', 7, NULL, NULL),
(790, 'Enfermería Pediátrica', 7, NULL, NULL),
(791, 'Introducción a la Administración en Enfermería', 7, NULL, NULL),
(792, 'Introducción a la Salud Comunitaria', 8, NULL, NULL),
(793, 'Anátomo-Fisiología I', 8, NULL, 1),
(794, 'Genética Humana', 8, NULL, NULL),
(795, 'Introducción a la Obstetricia', 8, NULL, NULL),
(796, 'Anátomo-Fisiología II', 8, NULL, NULL),
(797, 'Salud Comunitaria I', 8, NULL, NULL),
(798, 'Bioquímica', 8, NULL, NULL),
(799, 'Cultura y alfabetización digital en la universidad', 8, NULL, NULL),
(800, 'Introducción a la Nutrición', 8, NULL, NULL),
(801, 'Salud Comunitaria II', 8, NULL, NULL),
(802, 'Obstetricia I', 8, NULL, NULL),
(803, 'Salud Sexual y Reproductiva', 8, NULL, NULL),
(804, 'Obstetricia II', 8, NULL, NULL),
(805, 'Antropología', 8, NULL, NULL),
(806, 'Salud Comunitaria III', 8, NULL, NULL),
(807, 'Asignatura UNAHUR', 8, NULL, NULL),
(808, 'Psicología', 8, NULL, NULL),
(809, 'Obstetricia III', 8, NULL, NULL),
(810, 'Salud Comunitaria IV', 8, NULL, NULL),
(811, 'Deontología y aspectos legales del Ejercicio Profesional', 8, NULL, NULL),
(812, 'Obstetricia patológica', 8, NULL, NULL),
(813, 'Obstetricia IV', 8, NULL, NULL),
(814, 'Preparación Integral para la maternidad', 8, NULL, NULL),
(815, 'Microbiología', 8, NULL, NULL),
(816, 'Farmacología', 8, NULL, NULL),
(817, 'Evaluación de salud fetal', 8, NULL, NULL),
(818, 'Farmacología Obstétrica', 8, NULL, NULL),
(819, 'Práctica obstétrica integrada I', 8, NULL, NULL),
(820, 'Taller de investigación I', 8, NULL, NULL),
(821, 'Salud Comunitaria V', 8, NULL, NULL),
(822, 'Historia Sociosanitaria de la Salud', 8, NULL, NULL),
(823, 'Ética y desarrollo Profesional', 8, NULL, NULL),
(824, 'Puericultura', 8, NULL, NULL),
(825, 'Taller de investigación II', 8, NULL, NULL),
(826, 'Práctica obstétrica integrada II', 8, NULL, NULL),
(827, 'Lectura y Escritura Académica', NULL, 3, NULL),
(828, 'Matemática', NULL, 3, NULL),
(829, 'Introducción al Conocimiento de la Física y la Química', NULL, 3, NULL),
(830, 'Pensamiento Matemático', NULL, 4, NULL),
(831, 'Lectura y Escritura', NULL, 4, NULL),
(832, 'Vida Universitaria', NULL, 4, NULL),
(833, 'Programación Inicial', NULL, 7, NULL),
(834, 'Matemática Inicial', NULL, 7, NULL),
(835, 'Lectura Comprensiva', NULL, 7, NULL),
(836, 'Matemática ', NULL, 5, NULL),
(837, 'Lectura y Escritura', NULL, 5, NULL),
(838, 'Sociedad y Vida universitaria', NULL, 5, NULL),
(839, 'Introducción al Pensamiento Científico (IPC)', NULL, 6, NULL),
(840, 'Introducción al Conocimiento de la Sociedad y el Estado (ICSE)', NULL, 6, NULL),
(841, 'Inglés Básico', 3, NULL, NULL),
(842, 'Inglés Técnico', 3, NULL, 2),
(843, 'Informática', 3, NULL, 2),
(844, 'Técnicas Básicas de Laboratorio', 3, NULL, NULL),
(845, 'Matemática Aplicada', 3, NULL, NULL),
(846, 'Química General', 3, NULL, NULL),
(847, 'Química Orgánica', 3, NULL, NULL),
(848, 'Taller de Física Aplicada', 3, NULL, NULL),
(849, 'Fundamentos en Biología Celular y Molecular', 3, NULL, NULL),
(850, 'Biotecnología Clásica y Moderna', 3, NULL, NULL),
(851, 'Bioquímica', 3, NULL, NULL),
(852, 'Laboratorio de Química Instrumental', 3, NULL, NULL),
(853, 'Bases de la Microbiología Aplicada', 3, NULL, NULL),
(854, 'Técnicas Inmunológicas', 3, NULL, NULL),
(855, 'Estadística Aplicada', 3, NULL, NULL),
(856, 'Higiene y Seguridad Industrial', 3, NULL, NULL),
(857, 'Técnicas de Biología Molecular y Genética', 3, NULL, NULL),
(858, 'Producción por Fermentadores', 3, NULL, NULL),
(859, 'Modelos Animales y Bioterio', 3, NULL, NULL),
(860, 'Bioinformática', 3, NULL, NULL),
(861, 'Introducción a la Biotecnología Animal', 3, NULL, NULL),
(862, 'Introducción a la Biotecnología Vegetal', 3, NULL, NULL),
(863, 'Buenas Prácticas de Laboratorio', 3, NULL, NULL),
(864, 'Buenas Prácticas en la Producción Farmacéutica', 3, NULL, NULL),
(865, 'Facturación y Ventas', 3, NULL, NULL),
(866, 'Curso Complementario', 3, NULL, NULL),
(867, 'Introducción a la Tecnología de los Alimentos', 2, NULL, NULL),
(868, 'Matemática I', 2, NULL, NULL),
(869, 'Nuevos Entornos y Lenguajes: la producción del con', 2, NULL, NULL),
(870, 'Biología General', 2, NULL, NULL),
(871, 'Introducción a la Química', 2, NULL, NULL),
(872, 'Higiene y Seguridad', 2, NULL, NULL),
(873, 'Química General e Inorgánica', 2, NULL, NULL),
(874, 'Matemática II', 2, NULL, NULL),
(875, 'Inglés I', 2, NULL, NULL),
(876, 'Asignatura UNAHUR', 2, NULL, NULL),
(877, 'Introducción al Laboratorio de Análisis de Aliment', 2, NULL, NULL),
(878, 'Microbiología General', 2, NULL, NULL),
(879, 'Física', 2, NULL, NULL),
(880, 'Química Orgánica', 2, NULL, NULL),
(881, 'Microbiología de los Alimentos I', 2, NULL, NULL),
(882, 'Fisicoquímica de los Alimentos I', 2, NULL, NULL),
(883, 'Química de los Alimentos', 2, NULL, NULL),
(884, 'Laboratorio de química Instrumental y Analítica', 2, NULL, NULL),
(885, 'Taller de Bromatología y Análisis de la Calidad', 2, NULL, NULL),
(886, 'Gestión de la Calidad e Inocuidad de los Alimentos', 2, NULL, NULL),
(887, 'Operaciones Unitarias I', 2, NULL, NULL),
(888, 'Seminario General de Procesos Productivos de los A', 2, NULL, NULL),
(889, 'Introducción al Diseño', 4, NULL, NULL),
(890, 'Sistemas de representación gráfica', 4, NULL, NULL),
(891, 'Tecnología I', 4, NULL, NULL),
(892, 'Modelado', 4, NULL, NULL),
(893, 'Taller de Diseño I', 4, NULL, NULL),
(894, 'Matemática', 4, NULL, NULL),
(895, 'Morfología I', 4, NULL, NULL),
(896, 'Nuevos entornos y lenguajes: la producción del con', 4, NULL, NULL),
(897, 'Taller de Diseño II', 4, NULL, NULL),
(898, 'Tecnología y sociedad', 4, NULL, NULL),
(899, 'Taller de producción I', 4, NULL, NULL),
(900, 'Tecnología II', 4, NULL, NULL),
(901, 'Programación', 4, NULL, NULL),
(902, 'Ciencias aplicadas al diseño', 4, NULL, NULL),
(903, 'Taller de Diseño III', 4, NULL, NULL),
(904, 'Morfología II', 4, NULL, NULL),
(905, 'Tecnologías de fabricación digital I', 4, NULL, NULL),
(906, 'Asignatura UNAHUR', 4, NULL, NULL),
(907, 'Taller de Diseño IV', 4, NULL, NULL),
(908, 'Tecnología III', 4, NULL, NULL),
(909, 'Tecnologías de fabricación digital II', 4, NULL, NULL),
(910, 'Diseño e industria', 4, NULL, NULL),
(911, 'Inglés I', 4, NULL, NULL),
(912, 'Programación I', 5, NULL, NULL),
(913, 'Sistemas de Procesamientos de datos', 5, NULL, NULL),
(914, 'Matemática', 5, NULL, NULL),
(915, 'Ingles I', 5, NULL, NULL),
(916, 'Laboratorio de Computación I', 5, NULL, NULL),
(917, 'Programación II', 5, NULL, NULL),
(918, 'Arquitectura y Sistemas Operativos', 5, NULL, NULL),
(919, 'Estadística', 5, NULL, NULL),
(920, 'Metodología de la Investigación', 5, NULL, NULL),
(921, 'Inglés II', 5, NULL, NULL),
(922, 'Laboratorio de Computación II', 5, NULL, NULL),
(923, 'Programación III', 5, NULL, NULL),
(924, 'Organización Contable de la Empresa', 5, NULL, NULL),
(925, 'Organización Empresarial', 5, NULL, NULL),
(926, 'Elementos de Investigación Operativa', 5, NULL, NULL),
(927, 'Laboratorio de Computación III', 5, NULL, NULL),
(928, 'Diseño y administración de bases de datos', 5, NULL, NULL),
(929, 'Metodología de Sistemas I', 5, NULL, NULL),
(930, 'Legislación', 5, NULL, NULL),
(931, 'Laboratorio de Computación IV', 5, NULL, NULL),
(932, 'Práctica Profesional', 5, NULL, NULL),
(933, 'Economía de la cultura', 6, NULL, NULL),
(934, 'Cultura lúdica: jugar es humano', 6, NULL, NULL),
(935, 'Inglés II', 6, NULL, NULL),
(936, 'Planificación de negocios', 6, NULL, NULL),
(937, 'Metodología de la investigación II', 6, NULL, NULL),
(938, 'Inglés I', 6, NULL, NULL),
(939, 'Ética y liderazgo', 6, NULL, NULL),
(940, 'Taller proyectual', 6, NULL, NULL),
(941, 'Metodología de la investigación I', 6, NULL, NULL),
(942, 'Taller introductorio al diseño en 3D', 6, NULL, NULL),
(943, 'Q.A. (\"Control de calidad\")', 6, NULL, NULL),
(944, 'Producción y prácticas lúdicas II', 6, NULL, NULL),
(945, 'Internacionalización de proyectos', 6, NULL, NULL),
(946, 'Marketing digital', 6, NULL, NULL),
(947, 'Narrativas transmedia', 6, NULL, NULL),
(948, 'Taller de desarrollo de entornos virtuales', 6, NULL, NULL),
(949, 'Juegos serios II', 6, NULL, NULL),
(950, 'Modelos organizacionales', 6, NULL, NULL),
(951, 'Diseño lúdico II', 6, NULL, NULL),
(952, 'Taller de prototipado digital', 6, NULL, NULL),
(953, 'Taller de diseño y animación en 2D', 6, NULL, NULL),
(954, 'Juegos serios I', 6, NULL, NULL),
(955, 'Industria del videojuego', 6, NULL, NULL),
(956, 'Taller de diseño UIX/GUI', 6, NULL, NULL),
(957, 'Aspectos legales del desarrollo de videojuegos', 6, NULL, NULL),
(958, 'Producción y prácticas lúdicas I', 6, NULL, NULL),
(959, 'Historia de los videojuegos', 6, NULL, NULL),
(960, 'Gestión de proyectos', 6, NULL, NULL),
(961, 'Historia de la cultura II', 6, NULL, NULL),
(962, 'Historia del cine', 6, NULL, NULL),
(963, 'Pensamiento social argentino y latinoamericano', 6, NULL, NULL),
(964, 'Fundamentos de la programación I', 6, NULL, NULL),
(965, 'Diseño lúdico I', 6, NULL, NULL),
(966, 'Fundamentos de la programación II', 6, NULL, NULL),
(967, 'Historia de la cultura I', 6, NULL, NULL),
(968, 'La tecnología y sus usos', 6, NULL, NULL),
(969, 'Literatura y pensamiento', 6, NULL, NULL),
(970, 'Introducción al medio audiovisual', 6, NULL, NULL),
(971, 'Introducción a la comunicación', 6, NULL, NULL),
(972, 'Anatomofisiología', 7, NULL, NULL),
(973, 'Módulo Nº 1 Anatomía', 7, NULL, NULL),
(974, 'Módulo Nº2 Fisiología', 7, NULL, NULL),
(975, 'Química Biológica', 7, NULL, NULL),
(976, 'Física Biológica', 7, NULL, NULL),
(977, 'Introducción a la Enfermería en la Salud Pública', 7, NULL, NULL),
(978, 'Introducción a las Ciencias Psicosociales', 7, NULL, NULL),
(979, 'Enfermería Medica I', 7, NULL, NULL),
(980, 'Deontología I', 7, NULL, NULL),
(981, 'Microbiología y Parasitología', 7, NULL, NULL),
(982, 'Nutrición', 7, NULL, NULL),
(983, 'Enfermería En Salud Pública I', 7, NULL, NULL),
(984, 'Enfermería en Salud Materno Infantil', 7, NULL, NULL),
(985, 'Psicología Evolutiva', 7, NULL, NULL),
(986, 'Enfermería en Salud Mental', 7, NULL, NULL),
(987, 'Enfermería en Salud Pública II', 7, NULL, NULL),
(988, 'Enfermería Médica II', 7, NULL, NULL),
(989, 'Enfermería Quirúrgica', 7, NULL, NULL),
(990, 'Dietoterapia', 7, NULL, NULL),
(991, 'Enfermería Psiquiátrica', 7, NULL, NULL),
(992, 'Deontología II', 7, NULL, NULL),
(993, 'Enfermería Obstétrica', 7, NULL, NULL),
(994, 'Enfermería Pediátrica', 7, NULL, NULL),
(995, 'Introducción a la Administración en Enfermería', 7, NULL, NULL),
(996, 'Introducción a la Salud Comunitaria', 8, NULL, NULL),
(997, 'Anátomo-Fisiología I', 8, NULL, NULL),
(998, 'Genética Humana', 8, NULL, NULL),
(999, 'Introducción a la Obstetricia', 8, NULL, NULL),
(1000, 'Anátomo-Fisiología II', 8, NULL, NULL),
(1001, 'Salud Comunitaria I', 8, NULL, NULL),
(1002, 'Bioquímica', 8, NULL, NULL),
(1003, 'Cultura y alfabetización digital en la universidad', 8, NULL, NULL),
(1004, 'Introducción a la Nutrición', 8, NULL, NULL),
(1005, 'Salud Comunitaria II', 8, NULL, NULL),
(1006, 'Obstetricia I', 8, NULL, NULL),
(1007, 'Salud Sexual y Reproductiva', 8, NULL, NULL),
(1008, 'Obstetricia II', 8, NULL, NULL),
(1009, 'Antropología', 8, NULL, NULL),
(1010, 'Salud Comunitaria III', 8, NULL, NULL),
(1011, 'Asignatura UNAHUR', 8, NULL, NULL),
(1012, 'Psicología', 8, NULL, NULL),
(1013, 'Obstetricia III', 8, NULL, NULL),
(1014, 'Salud Comunitaria IV', 8, NULL, NULL),
(1015, 'Deontología y aspectos legales del Ejercicio Profesional', 8, NULL, NULL),
(1016, 'Obstetricia patológica', 8, NULL, NULL),
(1017, 'Obstetricia IV', 8, NULL, NULL),
(1018, 'Preparación Integral para la maternidad', 8, NULL, NULL),
(1019, 'Microbiología', 8, NULL, NULL),
(1020, 'Farmacología', 8, NULL, NULL),
(1021, 'Evaluación de salud fetal', 8, NULL, NULL),
(1022, 'Farmacología Obstétrica', 8, NULL, NULL),
(1023, 'Práctica obstétrica integrada I', 8, NULL, NULL),
(1024, 'Taller de investigación I', 8, NULL, NULL),
(1025, 'Salud Comunitaria V', 8, NULL, NULL),
(1026, 'Historia Sociosanitaria de la Salud', 8, NULL, NULL),
(1027, 'Ética y desarrollo Profesional', 8, NULL, NULL),
(1028, 'Puericultura', 8, NULL, NULL),
(1029, 'Taller de investigación II', 8, NULL, NULL),
(1030, 'Práctica obstétrica integrada II', 8, NULL, NULL),
(1031, 'Lectura y Escritura Académica', NULL, 3, NULL),
(1032, 'Matemática', NULL, 3, NULL),
(1033, 'Introducción al Conocimiento de la Física y la Química', NULL, 3, NULL),
(1034, 'Pensamiento Matemático', NULL, 4, NULL),
(1035, 'Lectura y Escritura', NULL, 4, NULL),
(1036, 'Vida Universitaria', NULL, 4, NULL),
(1037, 'Programación Inicial', NULL, 7, NULL),
(1038, 'Matemática Inicial', NULL, 7, NULL),
(1039, 'Lectura Comprensiva', NULL, 7, NULL),
(1040, 'Matemática ', NULL, 5, NULL),
(1041, 'Lectura y Escritura', NULL, 5, NULL),
(1042, 'Sociedad y Vida universitaria', NULL, 5, NULL),
(1043, 'Introducción al Pensamiento Científico (IPC)', NULL, 6, NULL),
(1044, 'Introducción al Conocimiento de la Sociedad y el Estado (ICSE)', NULL, 6, NULL),
(1045, 'Inglés Básico', 3, NULL, NULL),
(1046, 'Inglés Técnico', 3, NULL, 2),
(1047, 'Informática', 3, NULL, NULL),
(1048, 'Técnicas Básicas de Laboratorio', 3, NULL, NULL),
(1049, 'Matemática Aplicada', 3, NULL, NULL),
(1050, 'Química General', 3, NULL, NULL),
(1051, 'Química Orgánica', 3, NULL, NULL),
(1052, 'Taller de Física Aplicada', 3, NULL, NULL);
INSERT INTO `materias` (`id_materia`, `nombre`, `carrera_id`, `curso_pre_admision_id`, `profesor_id`) VALUES
(1053, 'Fundamentos en Biología Celular y Molecular', 3, NULL, NULL),
(1054, 'Biotecnología Clásica y Moderna', 3, NULL, NULL),
(1055, 'Bioquímica', 3, NULL, NULL),
(1056, 'Laboratorio de Química Instrumental', 3, NULL, NULL),
(1057, 'Bases de la Microbiología Aplicada', 3, NULL, NULL),
(1058, 'Técnicas Inmunológicas', 3, NULL, NULL),
(1059, 'Estadística Aplicada', 3, NULL, NULL),
(1060, 'Higiene y Seguridad Industrial', 3, NULL, NULL),
(1061, 'Técnicas de Biología Molecular y Genética', 3, NULL, NULL),
(1062, 'Producción por Fermentadores', 3, NULL, NULL),
(1063, 'Modelos Animales y Bioterio', 3, NULL, NULL),
(1064, 'Bioinformática', 3, NULL, NULL),
(1065, 'Introducción a la Biotecnología Animal', 3, NULL, NULL),
(1066, 'Introducción a la Biotecnología Vegetal', 3, NULL, NULL),
(1067, 'Buenas Prácticas de Laboratorio', 3, NULL, NULL),
(1068, 'Buenas Prácticas en la Producción Farmacéutica', 3, NULL, NULL),
(1069, 'Facturación y Ventas', 3, NULL, NULL),
(1070, 'Curso Complementario', 3, NULL, NULL),
(1071, 'Introducción a la Tecnología de los Alimentos', 2, NULL, NULL),
(1072, 'Matemática I', 2, NULL, NULL),
(1073, 'Nuevos Entornos y Lenguajes: la producción del con', 2, NULL, NULL),
(1074, 'Biología General', 2, NULL, NULL),
(1075, 'Introducción a la Química', 2, NULL, NULL),
(1076, 'Higiene y Seguridad', 2, NULL, NULL),
(1077, 'Química General e Inorgánica', 2, NULL, NULL),
(1078, 'Matemática II', 2, NULL, NULL),
(1079, 'Inglés I', 2, NULL, NULL),
(1080, 'Asignatura UNAHUR', 2, NULL, NULL),
(1081, 'Introducción al Laboratorio de Análisis de Aliment', 2, NULL, NULL),
(1082, 'Microbiología General', 2, NULL, NULL),
(1083, 'Física', 2, NULL, NULL),
(1084, 'Química Orgánica', 2, NULL, NULL),
(1085, 'Microbiología de los Alimentos I', 2, NULL, NULL),
(1086, 'Fisicoquímica de los Alimentos I', 2, NULL, NULL),
(1087, 'Química de los Alimentos', 2, NULL, NULL),
(1088, 'Laboratorio de química Instrumental y Analítica', 2, NULL, NULL),
(1089, 'Taller de Bromatología y Análisis de la Calidad', 2, NULL, NULL),
(1090, 'Gestión de la Calidad e Inocuidad de los Alimentos', 2, NULL, NULL),
(1091, 'Operaciones Unitarias I', 2, NULL, NULL),
(1092, 'Seminario General de Procesos Productivos de los A', 2, NULL, NULL),
(1093, 'Introducción al Diseño', 4, NULL, NULL),
(1094, 'Sistemas de representación gráfica', 4, NULL, NULL),
(1095, 'Tecnología I', 4, NULL, NULL),
(1096, 'Modelado', 4, NULL, NULL),
(1097, 'Taller de Diseño I', 4, NULL, NULL),
(1098, 'Matemática', 4, NULL, NULL),
(1099, 'Morfología I', 4, NULL, NULL),
(1100, 'Nuevos entornos y lenguajes: la producción del con', 4, NULL, NULL),
(1101, 'Taller de Diseño II', 4, NULL, NULL),
(1102, 'Tecnología y sociedad', 4, NULL, NULL),
(1103, 'Taller de producción I', 4, NULL, NULL),
(1104, 'Tecnología II', 4, NULL, NULL),
(1105, 'Programación', 4, NULL, NULL),
(1106, 'Ciencias aplicadas al diseño', 4, NULL, NULL),
(1107, 'Taller de Diseño III', 4, NULL, NULL),
(1108, 'Morfología II', 4, NULL, NULL),
(1109, 'Tecnologías de fabricación digital I', 4, NULL, NULL),
(1110, 'Asignatura UNAHUR', 4, NULL, NULL),
(1111, 'Taller de Diseño IV', 4, NULL, NULL),
(1112, 'Tecnología III', 4, NULL, NULL),
(1113, 'Tecnologías de fabricación digital II', 4, NULL, NULL),
(1114, 'Diseño e industria', 4, NULL, NULL),
(1115, 'Inglés I', 4, NULL, NULL),
(1116, 'Programación I', 5, NULL, NULL),
(1117, 'Sistemas de Procesamientos de datos', 5, NULL, NULL),
(1118, 'Matemática', 5, NULL, NULL),
(1119, 'Ingles I', 5, NULL, NULL),
(1120, 'Laboratorio de Computación I', 5, NULL, NULL),
(1121, 'Programación II', 5, NULL, NULL),
(1122, 'Arquitectura y Sistemas Operativos', 5, NULL, NULL),
(1123, 'Estadística', 5, NULL, NULL),
(1124, 'Metodología de la Investigación', 5, NULL, NULL),
(1125, 'Inglés II', 5, NULL, NULL),
(1126, 'Laboratorio de Computación II', 5, NULL, NULL),
(1127, 'Programación III', 5, NULL, NULL),
(1128, 'Organización Contable de la Empresa', 5, NULL, NULL),
(1129, 'Organización Empresarial', 5, NULL, NULL),
(1130, 'Elementos de Investigación Operativa', 5, NULL, NULL),
(1131, 'Laboratorio de Computación III', 5, NULL, NULL),
(1132, 'Diseño y administración de bases de datos', 5, NULL, NULL),
(1133, 'Metodología de Sistemas I', 5, NULL, NULL),
(1134, 'Legislación', 5, NULL, NULL),
(1135, 'Laboratorio de Computación IV', 5, NULL, NULL),
(1136, 'Práctica Profesional', 5, NULL, NULL),
(1137, 'Economía de la cultura', 6, NULL, NULL),
(1138, 'Cultura lúdica: jugar es humano', 6, NULL, NULL),
(1139, 'Inglés II', 6, NULL, NULL),
(1140, 'Planificación de negocios', 6, NULL, NULL),
(1141, 'Metodología de la investigación II', 6, NULL, NULL),
(1142, 'Inglés I', 6, NULL, NULL),
(1143, 'Ética y liderazgo', 6, NULL, NULL),
(1144, 'Taller proyectual', 6, NULL, NULL),
(1145, 'Metodología de la investigación I', 6, NULL, NULL),
(1146, 'Taller introductorio al diseño en 3D', 6, NULL, NULL),
(1147, 'Q.A. (\"Control de calidad\")', 6, NULL, NULL),
(1148, 'Producción y prácticas lúdicas II', 6, NULL, NULL),
(1149, 'Internacionalización de proyectos', 6, NULL, NULL),
(1150, 'Marketing digital', 6, NULL, NULL),
(1151, 'Narrativas transmedia', 6, NULL, NULL),
(1152, 'Taller de desarrollo de entornos virtuales', 6, NULL, NULL),
(1153, 'Juegos serios II', 6, NULL, NULL),
(1154, 'Modelos organizacionales', 6, NULL, NULL),
(1155, 'Diseño lúdico II', 6, NULL, NULL),
(1156, 'Taller de prototipado digital', 6, NULL, NULL),
(1157, 'Taller de diseño y animación en 2D', 6, NULL, NULL),
(1158, 'Juegos serios I', 6, NULL, NULL),
(1159, 'Industria del videojuego', 6, NULL, NULL),
(1160, 'Taller de diseño UIX/GUI', 6, NULL, NULL),
(1161, 'Aspectos legales del desarrollo de videojuegos', 6, NULL, NULL),
(1162, 'Producción y prácticas lúdicas I', 6, NULL, NULL),
(1163, 'Historia de los videojuegos', 6, NULL, NULL),
(1164, 'Gestión de proyectos', 6, NULL, NULL),
(1165, 'Historia de la cultura II', 6, NULL, NULL),
(1166, 'Historia del cine', 6, NULL, NULL),
(1167, 'Pensamiento social argentino y latinoamericano', 6, NULL, NULL),
(1168, 'Fundamentos de la programación I', 6, NULL, NULL),
(1169, 'Diseño lúdico I', 6, NULL, NULL),
(1170, 'Fundamentos de la programación II', 6, NULL, NULL),
(1171, 'Historia de la cultura I', 6, NULL, NULL),
(1172, 'La tecnología y sus usos', 6, NULL, NULL),
(1173, 'Literatura y pensamiento', 6, NULL, NULL),
(1174, 'Introducción al medio audiovisual', 6, NULL, NULL),
(1175, 'Introducción a la comunicación', 6, NULL, NULL),
(1176, 'Anatomofisiología', 7, NULL, NULL),
(1177, 'Módulo Nº 1 Anatomía', 7, NULL, NULL),
(1178, 'Módulo Nº2 Fisiología', 7, NULL, NULL),
(1179, 'Química Biológica', 7, NULL, NULL),
(1180, 'Física Biológica', 7, NULL, NULL),
(1181, 'Introducción a la Enfermería en la Salud Pública', 7, NULL, NULL),
(1182, 'Introducción a las Ciencias Psicosociales', 7, NULL, NULL),
(1183, 'Enfermería Medica I', 7, NULL, NULL),
(1184, 'Deontología I', 7, NULL, NULL),
(1185, 'Microbiología y Parasitología', 7, NULL, NULL),
(1186, 'Nutrición', 7, NULL, NULL),
(1187, 'Enfermería En Salud Pública I', 7, NULL, NULL),
(1188, 'Enfermería en Salud Materno Infantil', 7, NULL, NULL),
(1189, 'Psicología Evolutiva', 7, NULL, NULL),
(1190, 'Enfermería en Salud Mental', 7, NULL, NULL),
(1191, 'Enfermería en Salud Pública II', 7, NULL, NULL),
(1192, 'Enfermería Médica II', 7, NULL, NULL),
(1193, 'Enfermería Quirúrgica', 7, NULL, NULL),
(1194, 'Dietoterapia', 7, NULL, NULL),
(1195, 'Enfermería Psiquiátrica', 7, NULL, NULL),
(1196, 'Deontología II', 7, NULL, NULL),
(1197, 'Enfermería Obstétrica', 7, NULL, NULL),
(1198, 'Enfermería Pediátrica', 7, NULL, NULL),
(1199, 'Introducción a la Administración en Enfermería', 7, NULL, NULL),
(1200, 'Introducción a la Salud Comunitaria', 8, NULL, NULL),
(1201, 'Anátomo-Fisiología I', 8, NULL, NULL),
(1202, 'Genética Humana', 8, NULL, NULL),
(1203, 'Introducción a la Obstetricia', 8, NULL, NULL),
(1204, 'Anátomo-Fisiología II', 8, NULL, NULL),
(1205, 'Salud Comunitaria I', 8, NULL, NULL),
(1206, 'Bioquímica', 8, NULL, NULL),
(1207, 'Cultura y alfabetización digital en la universidad', 8, NULL, NULL),
(1208, 'Introducción a la Nutrición', 8, NULL, NULL),
(1209, 'Salud Comunitaria II', 8, NULL, NULL),
(1210, 'Obstetricia I', 8, NULL, NULL),
(1211, 'Salud Sexual y Reproductiva', 8, NULL, NULL),
(1212, 'Obstetricia II', 8, NULL, NULL),
(1213, 'Antropología', 8, NULL, NULL),
(1214, 'Salud Comunitaria III', 8, NULL, NULL),
(1215, 'Asignatura UNAHUR', 8, NULL, NULL),
(1216, 'Psicología', 8, NULL, NULL),
(1217, 'Obstetricia III', 8, NULL, NULL),
(1218, 'Salud Comunitaria IV', 8, NULL, NULL),
(1219, 'Deontología y aspectos legales del Ejercicio Profesional', 8, NULL, NULL),
(1220, 'Obstetricia patológica', 8, NULL, NULL),
(1221, 'Obstetricia IV', 8, NULL, NULL),
(1222, 'Preparación Integral para la maternidad', 8, NULL, NULL),
(1223, 'Microbiología', 8, NULL, NULL),
(1224, 'Farmacología', 8, NULL, NULL),
(1225, 'Evaluación de salud fetal', 8, NULL, NULL),
(1226, 'Farmacología Obstétrica', 8, NULL, NULL),
(1227, 'Práctica obstétrica integrada I', 8, NULL, NULL),
(1228, 'Taller de investigación I', 8, NULL, NULL),
(1229, 'Salud Comunitaria V', 8, NULL, NULL),
(1230, 'Historia Sociosanitaria de la Salud', 8, NULL, NULL),
(1231, 'Ética y desarrollo Profesional', 8, NULL, NULL),
(1232, 'Puericultura', 8, NULL, NULL),
(1233, 'Taller de investigación II', 8, NULL, NULL),
(1234, 'Práctica obstétrica integrada II', 8, NULL, NULL),
(1235, 'Lectura y Escritura Académica', NULL, 3, NULL),
(1236, 'Matemática', NULL, 3, NULL),
(1237, 'Introducción al Conocimiento de la Física y la Química', NULL, 3, NULL),
(1238, 'Pensamiento Matemático', NULL, 4, NULL),
(1239, 'Lectura y Escritura', NULL, 4, NULL),
(1240, 'Vida Universitaria', NULL, 4, NULL),
(1241, 'Programación Inicial', NULL, 7, NULL),
(1242, 'Matemática Inicial', NULL, 7, NULL),
(1243, 'Lectura Comprensiva', NULL, 7, NULL),
(1244, 'Matemática ', NULL, 5, NULL),
(1245, 'Lectura y Escritura', NULL, 5, NULL),
(1246, 'Sociedad y Vida universitaria', NULL, 5, NULL),
(1247, 'Introducción al Pensamiento Científico (IPC)', NULL, 6, NULL),
(1248, 'Introducción al Conocimiento de la Sociedad y el Estado (ICSE)', NULL, 6, NULL);

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
(1, 'fdsfds', 'dsfsdfds', NULL, NULL),
(2, 'fdsfds', 'faasfs', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjetas_disposicion`
--

CREATE TABLE `tarjetas_disposicion` (
  `id_tarjeta` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `turno_id` int(11) NOT NULL,
  `itinerario_id` int(11) NOT NULL,
  `materia_id` int(11) NOT NULL,
  `aula_id` int(11) NOT NULL,
  `profesor_id` int(11) NOT NULL,
  `estado` enum('activa','duplicada','programada') DEFAULT 'activa',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarjetas_disposicion`
--

INSERT INTO `tarjetas_disposicion` (`id_tarjeta`, `fecha`, `turno_id`, `itinerario_id`, `materia_id`, `aula_id`, `profesor_id`, `estado`, `fecha_creacion`) VALUES
(8, '2025-08-05', 1, 1, 793, 17, 1, 'activa', '2025-08-05 01:30:59'),
(9, '2025-08-06', 2, 3, 36, 17, 1, 'activa', '2025-08-05 01:31:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id_turno` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id_turno`, `nombre`, `hora_inicio`, `hora_fin`) VALUES
(1, 'Mañana', '08:00:00', '12:00:00'),
(2, 'Tarde', '13:00:00', '17:00:00'),
(3, 'Vespertino', '18:00:00', '22:00:00');

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
-- Indices de la tabla `itinerario`
--
ALTER TABLE `itinerario`
  ADD PRIMARY KEY (`id_itinerario`);

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
-- Indices de la tabla `tarjetas_disposicion`
--
ALTER TABLE `tarjetas_disposicion`
  ADD PRIMARY KEY (`id_tarjeta`),
  ADD KEY `fecha` (`fecha`),
  ADD KEY `turno_id` (`turno_id`),
  ADD KEY `itinerario_id` (`itinerario_id`),
  ADD KEY `materia_id` (`materia_id`),
  ADD KEY `aula_id` (`aula_id`),
  ADD KEY `profesor_id` (`profesor_id`),
  ADD KEY `estado` (`estado`),
  ADD KEY `idx_tarjetas_fecha_turno` (`fecha`,`turno_id`),
  ADD KEY `idx_tarjetas_aula_fecha` (`aula_id`,`fecha`),
  ADD KEY `idx_tarjetas_profesor_fecha` (`profesor_id`,`fecha`),
  ADD KEY `idx_tarjetas_estado` (`estado`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id_turno`);

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
  MODIFY `id_carrera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cursos_pre_admisiones`
--
ALTER TABLE `cursos_pre_admisiones`
  MODIFY `id_curso_pre_admision` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `itinerario`
--
ALTER TABLE `itinerario`
  MODIFY `id_itinerario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1249;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tarjetas_disposicion`
--
ALTER TABLE `tarjetas_disposicion`
  MODIFY `id_tarjeta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id_turno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_3` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materias_ibfk_4` FOREIGN KEY (`curso_pre_admision_id`) REFERENCES `cursos_pre_admisiones` (`id_curso_pre_admision`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `materias_ibfk_5` FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `tarjetas_disposicion`
--
ALTER TABLE `tarjetas_disposicion`
  ADD CONSTRAINT `tarjetas_disposicion_ibfk_1` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id_turno`),
  ADD CONSTRAINT `tarjetas_disposicion_ibfk_2` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id_aula`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tarjetas_disposicion_ibfk_3` FOREIGN KEY (`materia_id`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tarjetas_disposicion_ibfk_4` FOREIGN KEY (`itinerario_id`) REFERENCES `itinerario` (`id_itinerario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tarjetas_disposicion_ibfk_5` FOREIGN KEY (`profesor_id`) REFERENCES `profesores` (`id_profesor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `universidades`
--
ALTER TABLE `universidades`
  ADD CONSTRAINT `universidades_ibfk_2` FOREIGN KEY (`curso_pre_admision_id`) REFERENCES `cursos_pre_admisiones` (`id_curso_pre_admision`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
