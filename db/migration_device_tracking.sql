-- =====================================================
-- Awards Night 2025 - Device Tracking & Security Migration
-- Run this script to update the database schema
-- =====================================================

-- Add new columns to voters table for device tracking and voting status
ALTER TABLE `voters`
ADD COLUMN IF NOT EXISTS `device_fingerprint` VARCHAR(255) DEFAULT NULL COMMENT 'Device fingerprint hash for single device binding',
ADD COLUMN IF NOT EXISTS `device_info` TEXT DEFAULT NULL COMMENT 'Device user-agent and IP info',
ADD COLUMN IF NOT EXISTS `has_voted` TINYINT(1) DEFAULT 0 COMMENT 'Whether voter has submitted their vote',
ADD COLUMN IF NOT EXISTS `voted_at` DATETIME DEFAULT NULL COMMENT 'Timestamp when vote was submitted',
ADD COLUMN IF NOT EXISTS `session_token` VARCHAR(255) DEFAULT NULL COMMENT 'Unique session token for login validation',
ADD COLUMN IF NOT EXISTS `login_time` DATETIME DEFAULT NULL COMMENT 'Time of login',
ADD COLUMN IF NOT EXISTS `is_logged_in` TINYINT(1) DEFAULT 0 COMMENT 'Whether voter is currently logged in',
ADD COLUMN IF NOT EXISTS `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Account creation timestamp';

-- Create device tracking table to prevent device sharing
CREATE TABLE IF NOT EXISTS `device_registry` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `device_fingerprint` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Unique device identifier',
    `device_info` TEXT DEFAULT NULL COMMENT 'Device details (user-agent, IP, etc.)',
    `voter_id` INT DEFAULT NULL COMMENT 'First voter to use this device',
    `first_used_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `last_used_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `is_blocked` TINYINT(1) DEFAULT 0 COMMENT 'Whether device is blocked',
    INDEX `idx_device_fingerprint` (`device_fingerprint`),
    INDEX `idx_voter_id` (`voter_id`),
    FOREIGN KEY (`voter_id`) REFERENCES `voters`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create session tracking table for active sessions
CREATE TABLE IF NOT EXISTS `voter_sessions` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `voter_id` INT NOT NULL,
    `session_token` VARCHAR(255) NOT NULL UNIQUE,
    `device_fingerprint` VARCHAR(255) NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    INDEX `idx_voter_id` (`voter_id`),
    INDEX `idx_session_token` (`session_token`),
    INDEX `idx_device_fingerprint` (`device_fingerprint`),
    FOREIGN KEY (`voter_id`) REFERENCES `voters`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add indexes for performance
CREATE INDEX IF NOT EXISTS `idx_voters_device` ON `voters` (`device_fingerprint`);
CREATE INDEX IF NOT EXISTS `idx_voters_voted` ON `voters` (`has_voted`);
CREATE INDEX IF NOT EXISTS `idx_voters_session` ON `voters` (`session_token`);

-- =====================================================
-- VERIFICATION QUERIES (Run after migration)
-- =====================================================
-- DESCRIBE voters;
-- SHOW TABLES LIKE 'device_registry';
-- SHOW TABLES LIKE 'voter_sessions';
