--
-- Estructura de tabla para la tabla `articulos`
--

CREATE TABLE `articulos` (
  `a_id` int(11) NOT NULL AUTO_INCREMENT,
  `a_codigo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `a_nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `a_descripcion` text COLLATE utf8mb4_unicode_ci,
  `a_foto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_precio_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `a_precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `a_cantidad` int(10) UNSIGNED NOT NULL DEFAULT '500',
  `a_cantidad_minima` int(10) UNSIGNED NOT NULL DEFAULT '20',
  `a_id_categoria` int(11) NOT NULL DEFAULT '0',
  `a_id_proveedor` int(11) NOT NULL DEFAULT '0',
  `a_editando` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `a_habilitado` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`a_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indices de la tabla `articulos`
--
ALTER TABLE `articulos`
  ADD UNIQUE KEY `a_codigo` (`a_codigo`);

--
-- Volcado de datos para la tabla `articulos`
--

INSERT INTO `articulos` (`a_id`, `a_codigo`, `a_nombre`, `a_descripcion`, `a_foto`, `a_precio_compra`, `a_precio_venta`, `a_cantidad`, `a_cantidad_minima`, `a_id_categoria`, `a_id_proveedor`, `a_editando`, `a_habilitado`) VALUES
(1, 'M001', 'Sillón', NULL, NULL, '1000.00', '2800.00', 10, 5, 1, 0, '1', '1'),
(2, 'B0002', 'Heladera con frizzer', 'Heladera Con Freezer COLUMBIA Frio Directo 317 L. Htp 2334 Plata', '2.jpg', '14000.00', '25000.00', 10, 5, 2, 0, '1', '1');