-- =====================================================
-- Media Challenge Awards 2025 - Complete Database Schema
-- Alumni Voting System Security Update
-- =====================================================
-- Run this script to set up or update the database
-- Database: alumni_voting_db
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- CREATE DATABASE IF NOT EXISTS
-- =====================================================
CREATE DATABASE IF NOT EXISTS `alumni_voting_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `alumni_voting_db`;

-- =====================================================
-- ADMIN TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `firstname` VARCHAR(50) NOT NULL,
    `lastname` VARCHAR(50) NOT NULL,
    `photo` VARCHAR(150) DEFAULT '',
    `created_on` DATE NOT NULL DEFAULT CURRENT_DATE,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin if not exists (password: admin123)
INSERT INTO `admin` (`username`, `password`, `firstname`, `lastname`, `photo`, `created_on`)
SELECT 'admin', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/X4oaG3Y1jWXm0k6Iq', 'Media Challenge', 'Initiative', '', CURDATE()
WHERE NOT EXISTS (SELECT 1 FROM `admin` WHERE `username` = 'admin');

-- =====================================================
-- POSITIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `positions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(255) NOT NULL,
    `max_vote` INT(11) NOT NULL DEFAULT 1,
    `priority` INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- CANDIDATES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `candidates` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `position_id` INT(11) NOT NULL,
    `firstname` VARCHAR(50) NOT NULL,
    `lastname` VARCHAR(50) NOT NULL,
    `photo` VARCHAR(150) DEFAULT '',
    `platform` TEXT,
    PRIMARY KEY (`id`),
    INDEX `idx_position` (`position_id`),
    FOREIGN KEY (`position_id`) REFERENCES `positions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- VOTERS TABLE (Updated with security fields)
-- =====================================================
CREATE TABLE IF NOT EXISTS `voters` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `voters_id` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `firstname` VARCHAR(50) NOT NULL,
    `lastname` VARCHAR(50) NOT NULL,
    `photo` VARCHAR(150) DEFAULT '',
    `device_fingerprint` VARCHAR(255) DEFAULT NULL COMMENT 'Device fingerprint for single device binding',
    `device_info` TEXT DEFAULT NULL COMMENT 'Device details (user-agent, IP, etc.)',
    `has_voted` TINYINT(1) DEFAULT 0 COMMENT 'Whether voter has submitted their vote',
    `voted_at` DATETIME DEFAULT NULL COMMENT 'Timestamp when vote was submitted',
    `session_token` VARCHAR(255) DEFAULT NULL COMMENT 'Unique session token',
    `login_time` DATETIME DEFAULT NULL COMMENT 'Time of login',
    `is_logged_in` TINYINT(1) DEFAULT 0 COMMENT 'Whether voter is currently logged in',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `voters_id` (`voters_id`),
    INDEX `idx_device` (`device_fingerprint`),
    INDEX `idx_voted` (`has_voted`),
    INDEX `idx_session` (`session_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add columns if they don't exist (for existing installations)
-- MySQL doesn't support IF NOT EXISTS for ADD COLUMN, so we use stored procedure
DELIMITER //
CREATE PROCEDURE add_column_if_not_exists()
BEGIN
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'device_fingerprint') THEN
        ALTER TABLE `voters` ADD COLUMN `device_fingerprint` VARCHAR(255) DEFAULT NULL;
    END IF;

    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'device_info') THEN
        ALTER TABLE `voters` ADD COLUMN `device_info` TEXT DEFAULT NULL;
    END IF;

    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'has_voted') THEN
        ALTER TABLE `voters` ADD COLUMN `has_voted` TINYINT(1) DEFAULT 0;
    END IF;

    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'voted_at') THEN
        ALTER TABLE `voters` ADD COLUMN `voted_at` DATETIME DEFAULT NULL;
    END IF;

    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'session_token') THEN
        ALTER TABLE `voters` ADD COLUMN `session_token` VARCHAR(255) DEFAULT NULL;
    END IF;

    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'login_time') THEN
        ALTER TABLE `voters` ADD COLUMN `login_time` DATETIME DEFAULT NULL;
    END IF;

    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'is_logged_in') THEN
        ALTER TABLE `voters` ADD COLUMN `is_logged_in` TINYINT(1) DEFAULT 0;
    END IF;

    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'alumni_voting_db' AND TABLE_NAME = 'voters' AND COLUMN_NAME = 'created_at') THEN
        ALTER TABLE `voters` ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP;
    END IF;
END //
DELIMITER ;

CALL add_column_if_not_exists();
DROP PROCEDURE IF EXISTS add_column_if_not_exists;

-- =====================================================
-- VOTES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS `votes` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `voters_id` INT(11) NOT NULL,
    `candidate_id` INT(11) NOT NULL,
    `position_id` INT(11) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_voters` (`voters_id`),
    INDEX `idx_candidate` (`candidate_id`),
    INDEX `idx_position` (`position_id`),
    INDEX `idx_voter_position` (`voters_id`, `position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DEVICE REGISTRY TABLE (Prevent device sharing)
-- =====================================================
CREATE TABLE IF NOT EXISTS `device_registry` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `device_fingerprint` VARCHAR(255) NOT NULL,
    `device_info` TEXT DEFAULT NULL,
    `voter_id` INT(11) DEFAULT NULL,
    `first_used_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_used_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_blocked` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `device_fingerprint` (`device_fingerprint`),
    INDEX `idx_voter_id` (`voter_id`),
    FOREIGN KEY (`voter_id`) REFERENCES `voters`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- VOTER SESSIONS TABLE (Track active sessions)
-- =====================================================
CREATE TABLE IF NOT EXISTS `voter_sessions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `voter_id` INT(11) NOT NULL,
    `session_token` VARCHAR(255) NOT NULL,
    `device_fingerprint` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `session_token` (`session_token`),
    INDEX `idx_voter_id` (`voter_id`),
    INDEX `idx_device` (`device_fingerprint`),
    FOREIGN KEY (`voter_id`) REFERENCES `voters`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- LOGIN ATTEMPTS TABLE (Rate limiting & security)
-- =====================================================
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `identifier` VARCHAR(255) NOT NULL COMMENT 'Username or Voter ID',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `attempt_time` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `success` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `idx_identifier` (`identifier`),
    INDEX `idx_attempt_time` (`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- ERROR LOG TABLE (Track errors)
-- =====================================================
CREATE TABLE IF NOT EXISTS `error_log` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `error_type` VARCHAR(50) NOT NULL,
    `error_message` TEXT NOT NULL,
    `file` VARCHAR(255) DEFAULT NULL,
    `line` INT(11) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_type` (`error_type`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- CLEANUP OLD DATA (Optional - run periodically)
-- =====================================================
-- Delete old login attempts (older than 24 hours)
-- DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR);

-- Delete expired sessions (older than 7 days)
-- DELETE FROM voter_sessions WHERE expires_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- SELECT 'Tables Created Successfully' as Status;
-- SHOW TABLES;
-- DESCRIBE voters;
-- DESCRIBE device_registry;
-- DESCRIBE voter_sessions;
-- DESCRIBE login_attempts;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
