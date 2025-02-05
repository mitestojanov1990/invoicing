-- 1. Create the database (if it doesn't exist).
CREATE DATABASE IF NOT EXISTS angels_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

-- 2. Switch to the new database.
USE angels_db;

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL,
  `invoice_date` DATE NOT NULL,
  `to_name` VARCHAR(255) NOT NULL,
  `city` VARCHAR(255) NOT NULL,
  `invoice_type` TINYINT NOT NULL DEFAULT 1 COMMENT '1 = Faktura, 2 = Profaktura, 3 = Ponuda',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `invoice_lines` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` INT UNSIGNED NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantity` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `invoice_id_idx` (`invoice_id`),
  CONSTRAINT `fk_invoice_lines_to_invoices`
    FOREIGN KEY (`invoice_id`)
    REFERENCES `invoices` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
