-- admin_front table for visual_front module
CREATE TABLE `admin_front` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(250) DEFAULT NULL,
  `class` varchar(250) DEFAULT NULL,
  `active` int(11) DEFAULT 2,
  `color_primario` varchar(50) DEFAULT NULL,
  `color_secundario` varchar(50) DEFAULT NULL,
  `visible` int(11) DEFAULT 1,
  `img` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin_front` (`id`, `nombre`, `class`, `active`, `color_primario`, `color_secundario`, `visible`, `img`) VALUES
(1, 'Menú lateral', 'compact-wrapper', 1, '#7366ff', '#7366ff', 1, 'img/menu_lateral.png'),
(2, 'Menú Horizontal', 'horizontal-wrapper enterprice-type advance-layout', 2, '#497eb2', '#7366ff', 1, 'img/menu_horizontal.png'),
(3, 'Menú lateral (Iconos pequeños)', 'compact-sidebar compact-small material-icon', 2, '#5058a1', '#7366ff', 1, 'img/menu_lateral_iconos_minis.png'),
(4, 'Menú lateral (Flotante)', 'compact-wrapper modern-type', 2, '#7366ff', '#7366ff', 1, 'img/menu_lateral_flotante.png'),
(5, 'Sistema centrado', 'compact-wrapper box-layout', 2, '#7366ff', '#7366ff', 2, 'img/menu_centrado.png'),
(6, 'Menú lateral (Iconos grandes)', 'compact-sidebar', 2, '#7366ff', '#7366ff', 2, 'img/menu_lateral_iconos_grandes.png');