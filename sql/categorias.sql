--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `c_nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_observaciones` text COLLATE utf8mb4_unicode_ci,
  `c_habilitado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `c_editando` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`c_id`, `c_nombre`, `c_observaciones`, `c_habilitado`, `c_editando`) VALUES
(1, 'Muebles', NULL, '1', '1'),
(2, 'Linea Blanca', NULL, '1', '0'),
(3, 'TV, Audio y Video', NULL, '1', '0'),
(4, 'Electro y Aires', NULL, '1', '0'),
(5, 'Jardinería', NULL, '1', '0'),
(6, 'Bebés y Niños', NULL, '1', '0');
