-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-06-2023 a las 02:42:51
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sis_ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_seguridad_registros`
--

CREATE TABLE `bitacora_seguridad_registros` (
  `cod_registro_bitacora` int(11) NOT NULL,
  `nombre_tabla` varchar(50) NOT NULL,
  `cod_registro` int(11) NOT NULL,
  `fecha_hora_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `cod_usuario_registro` int(11) NOT NULL,
  `fecha_hora_edicion` timestamp NULL DEFAULT NULL,
  `cod_usuario_edicion` int(11) DEFAULT NULL,
  `fecha_hora_baja` timestamp NULL DEFAULT NULL,
  `cod_usuario_baja` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `cod_cargo` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `estado_mrcb` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `cargo`
--

INSERT INTO `cargo` (`cod_cargo`, `descripcion`, `estado_mrcb`) VALUES
(1, 'ADMINISTRADOR', 1),
(2, 'VENDEDOR', 1),
(3, 'LOGÍSTICA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_producto`
--

CREATE TABLE `categoria_producto` (
  `cod_categoria_producto` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cod_tipo_categoria` int(11) NOT NULL,
  `estado_mrcb` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cod_cliente` int(11) NOT NULL,
  `tipo_documento` char(1) NOT NULL,
  `numero_documento` varchar(11) DEFAULT NULL,
  `nombres` varchar(300) NOT NULL,
  `apellidos` varchar(200) DEFAULT '',
  `direccion` varchar(300) DEFAULT NULL,
  `correo` varchar(50) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `celular` varchar(10) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `numero_contacto` varchar(20) DEFAULT NULL,
  `estado_mrcb` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comisionista`
--

CREATE TABLE `comisionista` (
  `cod_comisionista` int(11) NOT NULL,
  `nombres` varchar(300) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `celular` varchar(11) NOT NULL,
  `estado_mrcb` tinyint(4) NOT NULL DEFAULT 1,
  `numero_documento` char(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comisionista_producto`
--

CREATE TABLE `comisionista_producto` (
  `cod_comisionista` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `tipo_comision` char(1) NOT NULL COMMENT 'M: Monto fijo, % Porcentaje',
  `valor_comision` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `cod_compra` int(11) NOT NULL,
  `cod_transaccion` int(11) NOT NULL,
  `cod_proveedor` int(11) NOT NULL,
  `tipo_pago` char(1) NOT NULL DEFAULT 'E',
  `tipo_tarjeta` char(1) DEFAULT NULL,
  `numero_comprobante` varchar(18) NOT NULL,
  `importe_total_compra` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_detalle`
--

CREATE TABLE `compra_detalle` (
  `cod_compra` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_vencimiento` date NOT NULL DEFAULT '0000-00-00',
  `lote` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion`
--

CREATE TABLE `cotizacion` (
  `cod_cotizacion` bigint(20) UNSIGNED NOT NULL,
  `cod_transaccion` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `razon_social_nombre` varchar(300) NOT NULL,
  `direccion_cliente` text NOT NULL,
  `correo_envio` varchar(50) NOT NULL,
  `numero_documento` varchar(15) DEFAULT NULL,
  `fecha_cotizacion` date NOT NULL,
  `cod_tipo_moneda` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `condicion_dias_credito` int(11) NOT NULL,
  `condicion_dias_validez` int(11) NOT NULL,
  `condicion_dias_entrega` int(11) NOT NULL,
  `condicion_delivery` decimal(10,2) DEFAULT NULL,
  `cta_bcp` varchar(50) DEFAULT NULL,
  `cta_bbva` varchar(50) DEFAULT NULL,
  `cta_bcp_cci` varchar(50) DEFAULT NULL,
  `cta_bbva_cci` varchar(50) DEFAULT NULL,
  `correlativo_cotizacion` int(11) NOT NULL,
  `estado_mrcb` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_detalle`
--

CREATE TABLE `cotizacion_detalle` (
  `cod_cotizacion_detalle` bigint(20) UNSIGNED NOT NULL,
  `cod_cotizacion` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `cod_unidad_medida` int(11) NOT NULL,
  `descripcion_producto` varchar(300) NOT NULL,
  `cod_marca` int(11) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `lote` varchar(20) DEFAULT NULL,
  `cantidad_item` int(11) NOT NULL,
  `valor_unitario` decimal(10,4) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `monto_igv` decimal(10,2) NOT NULL,
  `estado_mrcb` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento`
--

CREATE TABLE `descuento` (
  `cod_descuento` int(11) NOT NULL,
  `codigo_generado` varchar(6) NOT NULL,
  `tipo_descuento` char(1) NOT NULL DEFAULT 'M',
  `monto_descuento` decimal(10,2) NOT NULL,
  `usuario_uso` int(11) DEFAULT NULL,
  `fecha_hora_uso` timestamp NULL DEFAULT NULL,
  `estado_uso` tinyint(4) NOT NULL DEFAULT 0,
  `estado_mrcb` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `cod_marca` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado_mrcb` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `cod_permiso` int(11) NOT NULL,
  `es_menu_interfaz` tinyint(1) NOT NULL DEFAULT 1,
  `titulo_interfaz` varchar(50) NOT NULL,
  `url` varchar(50) DEFAULT NULL,
  `icono_interfaz` varchar(50) NOT NULL,
  `padre` int(11) DEFAULT NULL,
  `orden` int(11) DEFAULT NULL,
  `estado` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`cod_permiso`, `es_menu_interfaz`, `titulo_interfaz`, `url`, `icono_interfaz`, `padre`, `orden`, `estado`) VALUES
(1, 0, 'Mantenimientos', NULL, 'edit', NULL, NULL, 'A'),
(2, 1, 'Clientes', 'cliente.vista.php', '', 1, 0, 'A'),
(3, 1, 'Proveedor', 'proveedor.vista.php', '', 1, 1, 'A'),
(4, 1, 'Personal', 'personal.vista.php', '', 1, 2, 'A'),
(5, 1, 'Sucursal', 'sucursal.vista.php', '', 1, 3, 'A'),
(6, 1, 'Comisionistas', 'comisionista.vista.php', '', 1, 4, 'I'),
(7, 1, 'Cargos', 'cargo.vista.php', '', 1, 5, 'A'),
(8, 1, 'Productos', 'producto.vista.php', '', 1, 9, 'A'),
(9, 1, 'Tipo de Cat. Prod.', 'tipo.categoria.vista.php', '', 1, 8, 'A'),
(10, 1, 'Categoría de Productos', 'categoria.producto.vista.php', '', 1, 8, 'A'),
(11, 0, 'Transacciones', NULL, 'file-o', NULL, NULL, 'A'),
(12, 1, 'Generar Descuentos', 'descuento.vista.php', '', 11, 0, 'A'),
(13, 1, 'Ventas', 'ventas.vista.php', '', 11, 1, 'A'),
(14, 1, 'Compras', 'compras.vista.php', '', 11, 2, 'A'),
(15, 1, 'Almacén', 'almacen.vista.php', '', 11, 3, 'A'),
(16, 1, 'Catálogo', 'principal.vista.php', 'file', NULL, NULL, 'A'),
(17, 1, 'Permisos', 'permisos.vista.php', '', 1, 10, 'A'),
(18, 1, 'Roles', 'rol.vista.php', '', 1, 11, 'A'),
(19, 0, 'Reportes', NULL, 'edit', NULL, NULL, 'A'),
(20, 1, 'Reporte de Ventas', 'reporte.ventas.vista.php', '', 19, 0, 'A'),
(21, 1, 'Reporte de Stock', 'reporte.stock.vista.php', '', 19, 1, 'A'),
(22, 1, 'Producto más vendido', 'reporte.mas.vendido.vista.php', '', 19, 2, 'A'),
(23, 0, 'Facturación', NULL, 'money', NULL, NULL, 'A'),
(24, 1, 'Gestión Comprobantes', 'fact.comprobantes.vista.php', '', 23, 0, 'A'),
(25, 1, 'Marcas', 'marca.vista.php', '', 1, 6, 'A'),
(26, 1, 'Cotizaciones', 'cotizaciones.vista.php', '', 11, 4, 'A'),
(27, 1, 'Reporte Kardex', 'reporte.kardex.vista.php', '', 19, 3, 'A'),
(28, 1, 'Pagos Ventas', 'pagos.ventas.vista.php', '', 11, 5, 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso_rol`
--

CREATE TABLE `permiso_rol` (
  `cod_permiso` int(11) NOT NULL,
  `cod_rol` int(11) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `permiso_rol`
--

INSERT INTO `permiso_rol` (`cod_permiso`, `cod_rol`, `estado`) VALUES
(1, 1, 'A'),
(2, 1, 'A'),
(2, 2, 'A'),
(3, 1, 'A'),
(4, 1, 'A'),
(5, 1, 'A'),
(7, 1, 'A'),
(8, 1, 'A'),
(8, 3, 'A'),
(9, 1, 'A'),
(9, 3, 'A'),
(10, 1, 'A'),
(10, 3, 'A'),
(11, 1, 'A'),
(11, 3, 'A'),
(12, 1, 'A'),
(13, 1, 'A'),
(13, 2, 'A'),
(14, 1, 'A'),
(14, 3, 'A'),
(15, 1, 'A'),
(15, 3, 'A'),
(17, 1, 'A'),
(18, 1, 'A'),
(19, 1, 'A'),
(20, 1, 'A'),
(21, 1, 'A'),
(21, 2, 'A'),
(22, 1, 'A'),
(23, 1, 'A'),
(24, 1, 'A'),
(25, 1, 'A'),
(26, 1, 'A'),
(27, 1, 'A'),
(28, 1, 'A');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `cod_personal` int(11) NOT NULL,
  `dni` char(8) NOT NULL,
  `nombres` varchar(200) NOT NULL,
  `apellidos` varchar(200) NOT NULL,
  `celular` varchar(11) NOT NULL,
  `correo` varchar(60) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `cod_cargo` int(11) NOT NULL,
  `cod_rol` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL DEFAULT 1,
  `sexo` char(1) NOT NULL DEFAULT 'M',
  `acceso_sistema` tinyint(1) NOT NULL DEFAULT 0,
  `estado_activo` char(1) NOT NULL DEFAULT 'A',
  `clave` char(32) NOT NULL,
  `estado_mrcb` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`cod_personal`, `dni`, `nombres`, `apellidos`, `celular`, `correo`, `fecha_nacimiento`, `fecha_ingreso`, `cod_cargo`, `cod_rol`, `cod_sucursal`, `sexo`, `acceso_sistema`, `estado_activo`, `clave`, `estado_mrcb`) VALUES
(1, '48018866', 'ERIK', 'GUILLEN FLORES', '980031488', 'erik.ur.gf.10@gmail.com', '0000-00-00', '0000-00-00', 1, 1, 0, 'M', 1, 'A', '298e95f965b08f73eda14fae8a2515ee', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentacion`
--

CREATE TABLE `presentacion` (
  `cod_presentacion` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `estado_mrcb` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `presentacion`
--

INSERT INTO `presentacion` (`cod_presentacion`, `descripcion`, `estado_mrcb`) VALUES
(1, 'BOTELLA', 1),
(2, 'GALON', 1),
(3, 'BOLSA', 1),
(4, 'CAJA', 1),
(5, 'LENTE', 1),
(6, 'TUBO', 1),
(7, 'PAQUETE', 1),
(8, 'ROLLO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `cod_producto` int(11) NOT NULL,
  `codigo` varchar(6) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_unitario` decimal(11,3) DEFAULT NULL,
  `cod_unidad_medida` int(11) NOT NULL DEFAULT 7,
  `cod_presentacion` int(11) DEFAULT NULL,
  `cod_marca` int(11) DEFAULT NULL,
  `cod_categoria_producto` int(11) NOT NULL,
  `numero_imagen_principal` int(11) NOT NULL DEFAULT 1,
  `estado_mrcb` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_img`
--

CREATE TABLE `producto_img` (
  `cod_producto` int(11) NOT NULL,
  `numero_imagen` int(11) NOT NULL,
  `img_url` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `cod_proveedor` int(11) NOT NULL,
  `tipo_documento` char(2) NOT NULL,
  `numero_documento` varchar(11) DEFAULT NULL,
  `razon_social` varchar(250) NOT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `nombre_contacto` varchar(250) DEFAULT NULL,
  `celular_contacto` varchar(10) DEFAULT NULL,
  `estado_mrcb` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `cod_rol` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `estado_mrcb` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`cod_rol`, `descripcion`, `estado_mrcb`) VALUES
(1, 'ADMINISTRADOR', 1),
(2, 'VENDEDOR', 1),
(3, 'LOGÍSTICA', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_cache`
--

CREATE TABLE `sesiones_cache` (
  `cod_sesiones` int(11) NOT NULL,
  `ip_conexion` varchar(32) NOT NULL,
  `usuario_conexion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sesiones_cache`
--

INSERT INTO `sesiones_cache` (`cod_sesiones`, `ip_conexion`, `usuario_conexion`) VALUES
(4, '::1', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `cod_sucursal` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `direccion` text DEFAULT NULL,
  `telefono` varchar(9) DEFAULT NULL,
  `estado_mrcb` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`cod_sucursal`, `nombre`, `direccion`, `telefono`, `estado_mrcb`) VALUES
(0, 'TEMPORAL', NULL, NULL, 1),
(1, 'CHICLAYO', 'CAL. ARICA  NRO. REF INT. 3.PI   LAMBAYEQUE -  CHICLAYO  -  CHICLAYO', '07465064', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_personal`
--

CREATE TABLE `sucursal_personal` (
  `cod_sucursal` int(11) NOT NULL,
  `cod_personal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_producto`
--

CREATE TABLE `sucursal_producto` (
  `cod_sucursal_producto` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `fecha_vencimiento` date NOT NULL DEFAULT '0000-00-00',
  `lote` varchar(20) NOT NULL DEFAULT '',
  `precio_entrada` decimal(10,3) NOT NULL DEFAULT 0.000,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_producto_historial`
--

CREATE TABLE `sucursal_producto_historial` (
  `cod_historial` bigint(20) UNSIGNED NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `fecha_vencimiento` date NOT NULL DEFAULT '0000-00-00',
  `lote` varchar(20) NOT NULL,
  `cod_sucursal` int(11) NOT NULL DEFAULT 1,
  `precio_entrada` decimal(10,3) DEFAULT NULL,
  `precio_salida` decimal(10,3) DEFAULT NULL,
  `cod_transaccion` int(11) DEFAULT NULL,
  `tipo_movimiento` char(1) NOT NULL COMMENT '-- E: Entrada: S : Salida',
  `cantidad` int(11) NOT NULL,
  `cod_sucursal_transferencia` int(11) DEFAULT NULL,
  `fecha_movimiento` date NOT NULL,
  `estado_mrcb` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_transferencia`
--

CREATE TABLE `sucursal_transferencia` (
  `cod_sucursal_transferencia` int(11) NOT NULL,
  `cod_sucursal_origen` int(11) NOT NULL,
  `cod_sucursal_destino` int(11) NOT NULL,
  `estado_mrcb` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_categoria`
--

CREATE TABLE `tipo_categoria` (
  `cod_tipo_categoria` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado_mrcb` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_comprobante`
--

CREATE TABLE `tipo_comprobante` (
  `cod_tipo_comprobante` char(2) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `abrev` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tipo_comprobante`
--

INSERT INTO `tipo_comprobante` (`cod_tipo_comprobante`, `nombre`, `abrev`) VALUES
('00', 'VOUCHER', 'V'),
('01', 'FACTURA', 'F'),
('03', 'BOLETA DE VENTA', 'B'),
('07', 'NOTA DE CRÉDITO', NULL),
('08', 'NOTA DE DÉBITO', NULL),
('CO', 'COTIZACION', 'C');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `cod_tipo_documento` char(1) NOT NULL,
  `descripcion` varchar(40) NOT NULL,
  `abrev` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tipo_documento`
--

INSERT INTO `tipo_documento` (`cod_tipo_documento`, `descripcion`, `abrev`) VALUES
('0', 'DOC.TRIB.NO.DOM.SN.RUC', 'S/D'),
('1', 'DOC. NACIONAL DE IDENTIDAD', 'DNI'),
('4', 'CARNET DE EXTRANJERIA', NULL),
('6', 'REG. UNICO CONTRIBUYENTES', 'RUC'),
('7', 'PASAPORTE', NULL),
('A', 'CED. DIPLOMATICA DE IDENTIDAD', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_moneda`
--

CREATE TABLE `tipo_moneda` (
  `cod_tipo_moneda` int(11) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `abrev` char(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tipo_moneda`
--

INSERT INTO `tipo_moneda` (`cod_tipo_moneda`, `nombre`, `abrev`) VALUES
(1, 'SOLES', 'PEN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaccion`
--

CREATE TABLE `transaccion` (
  `cod_transaccion` int(11) NOT NULL,
  `cod_tipo_documento` char(1) NOT NULL,
  `cod_tipo_comprobante` char(2) DEFAULT '03',
  `serie` char(3) DEFAULT NULL,
  `correlativo` int(11) DEFAULT 1,
  `cod_sucursal` int(11) DEFAULT NULL,
  `fecha_transaccion` date NOT NULL,
  `observaciones` text DEFAULT NULL,
  `guias_remision` varchar(100) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1: activo, 0:  anulado',
  `estado_generado` tinyint(1) NOT NULL DEFAULT 0,
  `estado_sunat` char(1) NOT NULL DEFAULT 'N' COMMENT 'N: No Enviado, A: Aceptado, R: Rechazado',
  `cdr` text DEFAULT NULL,
  `hash_cpe` text DEFAULT NULL,
  `hash_cdr` text DEFAULT NULL,
  `fecha_envio_sunat` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `cod_unidad_medida` int(11) NOT NULL,
  `codigo_sunat` char(2) NOT NULL,
  `codigo_ece` varchar(3) NOT NULL,
  `descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`cod_unidad_medida`, `codigo_sunat`, `codigo_ece`, `descripcion`) VALUES
(1, '01', 'KGM', 'KILOGRAMOS'),
(2, '02', 'LBR', 'LIBRAS'),
(4, '04', 'STN', 'TONELADAS'),
(6, '06', 'GRM', 'GRAMOS'),
(7, '07', 'NIU', 'UNIDADES'),
(8, '08', 'LTR', 'LITROS'),
(9, '09', 'GAL', 'GALONES'),
(10, '10', 'BLL', 'BARRILES'),
(11, '11', 'CA', 'LATAS'),
(12, '12', 'BX', 'CAJAS'),
(13, '13', 'MLD', 'MILLARES'),
(14, '14', 'MTQ', 'METROS CUBICOS'),
(15, '15', 'MTR', 'METROS'),
(16, 'PR', 'PR', 'PAR'),
(17, '17', 'H87', 'PIEZA'),
(18, '18', 'XRO', 'ROLLOS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variable_constante`
--

CREATE TABLE `variable_constante` (
  `nombre_variable` varchar(50) NOT NULL,
  `valor_variable` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `variable_constante`
--

INSERT INTO `variable_constante` (`nombre_variable`, `valor_variable`) VALUES
('IGV', '0.18'),
('INCLUIR_IGV', '1'),
('correlativo_boletas', '79'),
('correlativo_cotizacion', '304');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `cod_venta` int(11) NOT NULL,
  `cod_transaccion` int(11) NOT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `razon_social_nombre` varchar(250) DEFAULT NULL,
  `direccion_cliente` varchar(500) DEFAULT NULL,
  `tipo_pago` char(1) NOT NULL,
  `monto_efectivo` decimal(10,2) NOT NULL,
  `monto_tarjeta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tipo_tarjeta` char(1) DEFAULT NULL,
  `monto_credito` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fecha_vencimiento` date DEFAULT NULL,
  `importe_total_venta` decimal(10,2) NOT NULL,
  `correo_envio` varchar(300) NOT NULL,
  `numero_documento` varchar(11) DEFAULT NULL,
  `cod_descuento_global` int(11) DEFAULT NULL,
  `descuentos_globales` decimal(10,2) NOT NULL,
  `total_descuentos` decimal(10,2) NOT NULL,
  `cod_tipo_moneda` int(11) NOT NULL,
  `tipo_operacion` char(2) DEFAULT '01',
  `total_valor_venta` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_valor_venta_bruto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_gravadas` decimal(10,2) NOT NULL,
  `sumatoria_igv` decimal(10,2) NOT NULL,
  `cod_comisionista` int(11) DEFAULT NULL,
  `numero_voucher` varchar(20) DEFAULT NULL,
  `porcentaje_igv` decimal(5,2) NOT NULL DEFAULT 18.00,
  `porcentaje_descuento_comprobante` decimal(8,5) NOT NULL DEFAULT 0.00000,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_descuentos_comprobante` decimal(10,2) NOT NULL DEFAULT 0.00,
  `descuento_global_comprobante` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_comision_producto`
--

CREATE TABLE `venta_comision_producto` (
  `cod_venta` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `tipo_comision` char(1) NOT NULL COMMENT 'M: MONTO FIJO. P: PORCENTAJE',
  `valor_comision` decimal(10,2) NOT NULL,
  `monto_comision` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_credito`
--

CREATE TABLE `venta_credito` (
  `cod_venta` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tipo_deuda` int(11) NOT NULL,
  `pendiente` int(11) NOT NULL,
  `fecha_registro` date NOT NULL,
  `cod_venta_credito` bigint(20) UNSIGNED NOT NULL,
  `observaciones` text DEFAULT NULL,
  `estado_mrcb` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `venta_credito`
--

INSERT INTO `venta_credito` (`cod_venta`, `monto`, `tipo_deuda`, `pendiente`, `fecha_registro`, `cod_venta_credito`, `observaciones`, `estado_mrcb`) VALUES
(1, 30.00, -1, 30, '2022-06-16', 6, NULL, 1),
(1, 10.00, 1, 20, '2022-06-16', 7, 'ME PAGARON EN X....', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalle`
--

CREATE TABLE `venta_detalle` (
  `cod_venta` int(11) NOT NULL,
  `item` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `lote` varchar(20) NOT NULL,
  `valor_unitario` decimal(12,4) NOT NULL,
  `cantidad_item` int(11) NOT NULL DEFAULT 1,
  `cod_descuento` int(11) DEFAULT NULL,
  `tipo_descuento` char(1) DEFAULT NULL COMMENT '--P : Porcentaje ,  M: Monto directo',
  `monto_descuento` decimal(10,2) DEFAULT NULL,
  `descripcion_producto` text DEFAULT NULL,
  `cod_sunat` int(11) DEFAULT NULL,
  `descuentos` decimal(10,2) DEFAULT 0.00,
  `monto_igv` decimal(10,2) NOT NULL DEFAULT 0.00,
  `afectacion_igv` char(2) NOT NULL DEFAULT '10',
  `monto_isc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_venta_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_venta_bruto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_venta` decimal(10,2) NOT NULL,
  `porcentaje_descuento_comprobante` decimal(8,5) NOT NULL DEFAULT 0.00000,
  `descuento_comprobante` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cadena_stock_producto` text NOT NULL,
  `costo_producto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cod_unidad_medida` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_pago`
--

CREATE TABLE `venta_pago` (
  `cod_venta_pago` bigint(20) UNSIGNED NOT NULL,
  `cod_venta` int(11) NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `monto_pagado` int(11) NOT NULL,
  `cod_venta_credito` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora_seguridad_registros`
--
ALTER TABLE `bitacora_seguridad_registros`
  ADD PRIMARY KEY (`cod_registro_bitacora`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`cod_cargo`);

--
-- Indices de la tabla `categoria_producto`
--
ALTER TABLE `categoria_producto`
  ADD PRIMARY KEY (`cod_categoria_producto`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cod_cliente`);

--
-- Indices de la tabla `comisionista`
--
ALTER TABLE `comisionista`
  ADD PRIMARY KEY (`cod_comisionista`);

--
-- Indices de la tabla `comisionista_producto`
--
ALTER TABLE `comisionista_producto`
  ADD PRIMARY KEY (`cod_comisionista`,`cod_producto`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`cod_compra`);

--
-- Indices de la tabla `compra_detalle`
--
ALTER TABLE `compra_detalle`
  ADD PRIMARY KEY (`cod_compra`,`item`);

--
-- Indices de la tabla `cotizacion`
--
ALTER TABLE `cotizacion`
  ADD PRIMARY KEY (`cod_cotizacion`),
  ADD UNIQUE KEY `id_cotizacion` (`cod_cotizacion`),
  ADD KEY `cod_tipo_moneda` (`cod_tipo_moneda`),
  ADD KEY `cod_transaccion` (`cod_transaccion`);

--
-- Indices de la tabla `cotizacion_detalle`
--
ALTER TABLE `cotizacion_detalle`
  ADD PRIMARY KEY (`cod_cotizacion_detalle`),
  ADD UNIQUE KEY `id_cotizacion_detalle` (`cod_cotizacion_detalle`),
  ADD KEY `cod_unidad_medida` (`cod_unidad_medida`);

--
-- Indices de la tabla `descuento`
--
ALTER TABLE `descuento`
  ADD PRIMARY KEY (`cod_descuento`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`cod_marca`),
  ADD UNIQUE KEY `id_marca` (`cod_marca`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`cod_permiso`);

--
-- Indices de la tabla `permiso_rol`
--
ALTER TABLE `permiso_rol`
  ADD PRIMARY KEY (`cod_permiso`,`cod_rol`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`cod_personal`);

--
-- Indices de la tabla `presentacion`
--
ALTER TABLE `presentacion`
  ADD PRIMARY KEY (`cod_presentacion`),
  ADD UNIQUE KEY `cod_presentacion` (`cod_presentacion`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`cod_producto`),
  ADD KEY `cod_presentacion` (`cod_presentacion`);

--
-- Indices de la tabla `producto_img`
--
ALTER TABLE `producto_img`
  ADD PRIMARY KEY (`cod_producto`,`numero_imagen`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`cod_proveedor`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`cod_rol`);

--
-- Indices de la tabla `sesiones_cache`
--
ALTER TABLE `sesiones_cache`
  ADD PRIMARY KEY (`cod_sesiones`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`cod_sucursal`);

--
-- Indices de la tabla `sucursal_personal`
--
ALTER TABLE `sucursal_personal`
  ADD PRIMARY KEY (`cod_sucursal`,`cod_personal`);

--
-- Indices de la tabla `sucursal_producto`
--
ALTER TABLE `sucursal_producto`
  ADD PRIMARY KEY (`cod_sucursal_producto`),
  ADD KEY `cod_producto` (`cod_producto`,`fecha_vencimiento`,`lote`,`precio_entrada`);

--
-- Indices de la tabla `sucursal_producto_historial`
--
ALTER TABLE `sucursal_producto_historial`
  ADD PRIMARY KEY (`cod_historial`),
  ADD UNIQUE KEY `cod_historial` (`cod_historial`);

--
-- Indices de la tabla `sucursal_transferencia`
--
ALTER TABLE `sucursal_transferencia`
  ADD PRIMARY KEY (`cod_sucursal_transferencia`);

--
-- Indices de la tabla `tipo_categoria`
--
ALTER TABLE `tipo_categoria`
  ADD PRIMARY KEY (`cod_tipo_categoria`);

--
-- Indices de la tabla `tipo_comprobante`
--
ALTER TABLE `tipo_comprobante`
  ADD PRIMARY KEY (`cod_tipo_comprobante`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`cod_tipo_documento`);

--
-- Indices de la tabla `tipo_moneda`
--
ALTER TABLE `tipo_moneda`
  ADD PRIMARY KEY (`cod_tipo_moneda`);

--
-- Indices de la tabla `transaccion`
--
ALTER TABLE `transaccion`
  ADD PRIMARY KEY (`cod_transaccion`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`cod_unidad_medida`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`cod_venta`);

--
-- Indices de la tabla `venta_comision_producto`
--
ALTER TABLE `venta_comision_producto`
  ADD PRIMARY KEY (`cod_venta`,`item`);

--
-- Indices de la tabla `venta_credito`
--
ALTER TABLE `venta_credito`
  ADD PRIMARY KEY (`cod_venta_credito`),
  ADD UNIQUE KEY `cod_transaccion` (`cod_venta_credito`);

--
-- Indices de la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD PRIMARY KEY (`cod_venta`,`item`);

--
-- Indices de la tabla `venta_pago`
--
ALTER TABLE `venta_pago`
  ADD PRIMARY KEY (`cod_venta_pago`),
  ADD UNIQUE KEY `cod_pago_venta` (`cod_venta_pago`),
  ADD KEY `cod_venta` (`cod_venta`),
  ADD KEY `cod_venta_credito` (`cod_venta_credito`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora_seguridad_registros`
--
ALTER TABLE `bitacora_seguridad_registros`
  MODIFY `cod_registro_bitacora` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2208;

--
-- AUTO_INCREMENT de la tabla `comisionista_producto`
--
ALTER TABLE `comisionista_producto`
  MODIFY `cod_comisionista` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cotizacion`
--
ALTER TABLE `cotizacion`
  MODIFY `cod_cotizacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `cotizacion_detalle`
--
ALTER TABLE `cotizacion_detalle`
  MODIFY `cod_cotizacion_detalle` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `cod_marca` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `cod_permiso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `presentacion`
--
ALTER TABLE `presentacion`
  MODIFY `cod_presentacion` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `sesiones_cache`
--
ALTER TABLE `sesiones_cache`
  MODIFY `cod_sesiones` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sucursal_producto`
--
ALTER TABLE `sucursal_producto`
  MODIFY `cod_sucursal_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1059;

--
-- AUTO_INCREMENT de la tabla `sucursal_producto_historial`
--
ALTER TABLE `sucursal_producto_historial`
  MODIFY `cod_historial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `cod_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `venta_credito`
--
ALTER TABLE `venta_credito`
  MODIFY `cod_venta_credito` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `venta_pago`
--
ALTER TABLE `venta_pago`
  MODIFY `cod_venta_pago` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
