-- Tablas faltantes para gestion_usuarios en voley_plus

-- sexo
CREATE TABLE `sexo` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `sexo` (`nombre`, `slug`) VALUES 
('Masculino', 'masculino'), ('Femenino', 'femenino'), ('Otro', 'otro');

-- estado
CREATE TABLE `estado` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `estado` (`nombre`, `slug`) VALUES 
('Activo', 'activo'), ('Inactivo', 'inactivo'), ('Pendiente', 'pendiente');

-- plantas
CREATE TABLE `plantas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `visible` CHAR(1) DEFAULT 'S'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `plantas` (`nombre`, `slug`, `visible`) VALUES 
('Planta Principal', 'planta-principal', 'S'), ('Planta Norte', 'planta-norte', 'S');

-- tipo_contrato
CREATE TABLE `tipo_contrato` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `visible` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT INTO `tipo_contrato` (`nombre`, `visible`) VALUES 
('Tiempo Completo', 1), ('Medio Tiempo', 1), ('Contrato Fijo', 1), ('Practicas', 1);

-- datos_laborales
CREATE TABLE `datos_laborales` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `persona_id` INT NOT NULL UNIQUE,
  `cargo` VARCHAR(100) DEFAULT '',
  `nombre_completo` VARCHAR(200) DEFAULT '',
  `fecha_ingreso` DATE DEFAULT NULL,
  `salario` DECIMAL(12,2) DEFAULT 0,
  `auxilio_alimentacion` DECIMAL(12,2) DEFAULT 0,
  `tipo_contrato` VARCHAR(100) DEFAULT '',
  FOREIGN KEY (`persona_id`) REFERENCES `persona`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;