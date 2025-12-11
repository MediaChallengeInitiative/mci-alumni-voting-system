-- =====================================================
-- 2025 Media Challenge Awards - Alumni Nominations
-- Database Setup Script
-- =====================================================
-- Run this script in phpMyAdmin or MySQL CLI
-- Database: alumni_voting_db
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- =====================================================
-- STEP 1: Clear existing data (preserves table structure)
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `votes`;
TRUNCATE TABLE `candidates`;
TRUNCATE TABLE `positions`;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- STEP 2: INSERT AWARD CATEGORIES (Positions)
-- =====================================================

INSERT INTO `positions` (`id`, `description`, `max_vote`, `priority`) VALUES
(1, 'Excellence in Communication', 1, 1),
(2, 'Outstanding Print Journalism', 1, 2),
(3, 'Exceptional TV Broadcast Achievement', 1, 3),
(4, 'Exceptional Radio Broadcast Achievement', 1, 4),
(5, 'Distinguished Photography', 1, 5),
(6, 'Media Innovation', 1, 6);

-- =====================================================
-- STEP 3: INSERT NOMINEES (Candidates)
-- =====================================================

-- Category 1: Excellence in Communication (5 nominees)
INSERT INTO `candidates` (`position_id`, `firstname`, `lastname`, `photo`, `platform`) VALUES
(1, 'Julius', 'Bukyana', '', 'Victoria University - Demonstrating exceptional communication skills in academic and professional settings.'),
(1, 'Dismas', 'Nuwaine', '', 'Uganda Revenue Authority (URA) - Excellence in public communication and stakeholder engagement.'),
(1, 'Patrick', 'Ssentongo', '', 'National Theatre - Outstanding contributions to arts communication and cultural advocacy.'),
(1, 'Christine', 'Kabazira', '', 'Uganda Human Rights Commission - Championing human rights communication and public awareness.'),
(1, 'Olivia', 'Komugisha', '', 'Reproductive Health Uganda - Leading health communication initiatives and community outreach.');

-- Category 2: Outstanding Print Journalism (4 nominees)
INSERT INTO `candidates` (`position_id`, `firstname`, `lastname`, `photo`, `platform`) VALUES
(2, 'Patrick', 'Ssentongo', '', 'Daily Monitor - Delivering impactful print journalism and investigative reporting.'),
(2, 'Irankunda', 'Gloria', '', 'Daily Monitor - Excellence in news writing and feature stories.'),
(2, 'Akullu', 'Felly', '', 'Daily Monitor - Outstanding contributions to print media and storytelling.'),
(2, 'Geoffrey', 'Mutumba', '', 'Daily Monitor - Distinguished work in print journalism and photography.');

-- Category 3: Exceptional TV Broadcast Achievement (6 nominees)
INSERT INTO `candidates` (`position_id`, `firstname`, `lastname`, `photo`, `platform`) VALUES
(3, 'Edgar Mathew', 'Karuhanga', '', 'NBS TV - Exceptional television broadcasting and news presentation.'),
(3, 'Nagitta', 'Dorothy', '', 'NTV Uganda - Outstanding TV journalism and audience engagement.'),
(3, 'Ijjo', 'David', '', 'NTV Uganda - Excellence in television news reporting and storytelling.'),
(3, 'Daniel', 'Ayebare', '', 'NBS TV - Distinguished contributions to broadcast journalism.'),
(3, 'Sulaiman', 'Ssebugwawo', '', 'NBS TV - Excellence in television production and presentation.'),
(3, 'Tracey', 'Kansiime', '', 'NBS TV - Outstanding achievements in TV broadcasting and audience connection.');

-- Category 4: Exceptional Radio Broadcast Achievement (6 nominees)
INSERT INTO `candidates` (`position_id`, `firstname`, `lastname`, `photo`, `platform`) VALUES
(4, 'Joseph El Shadai', 'Sebandeke', '', 'Salt FM - Exceptional radio broadcasting and listener engagement.'),
(4, 'Faiza', 'Fabz', '', 'KFM - Outstanding radio presentation and content creation.'),
(4, 'Yadda', 'Wanjiku', '', 'KFM - Excellence in radio journalism and entertainment.'),
(4, 'Maria', 'Kansinga', '', 'Galaxy FM - Distinguished radio broadcasting and community impact.'),
(4, 'Wagaba Moses', 'Morgan', '', 'Dembe FM - Excellence in radio programming and audience reach.'),
(4, 'Florence', 'Kabagenyi', '', 'Galaxy FM - Outstanding achievements in radio broadcast journalism.');

-- Category 5: Distinguished Photography (5 nominees)
INSERT INTO `candidates` (`position_id`, `firstname`, `lastname`, `photo`, `platform`) VALUES
(5, 'Mutumba', 'Geoffrey', '', 'Daily Monitor - Excellence in photojournalism and visual storytelling.'),
(5, 'Serina', 'Kirabo', '', 'Freelance - Distinguished photography work and creative vision.'),
(5, 'Japheth Godwin', 'Walakira', '', 'Daily Monitor - Outstanding photojournalism and documentary photography.'),
(5, 'Moses', 'Sserunjogi', '', 'Freelance - Excellence in photography and visual arts.'),
(5, 'Isano', 'Francis', '', 'Next Media Services - Distinguished work in media photography and content creation.');

-- Category 6: Media Innovation (6 nominees)
INSERT INTO `candidates` (`position_id`, `firstname`, `lastname`, `photo`, `platform`) VALUES
(6, 'Cophi', 'Samuel', '', 'Content House - Pioneering innovative media content and digital strategies.'),
(6, 'Katongole', 'Emmanuel', '', 'Insights X-Space Series - Innovation in digital media spaces and audience engagement.'),
(6, 'Arinaitwe', 'Hannah', '', 'Young and Branded Podcast - Innovative podcast creation and youth media engagement.'),
(6, 'Kevin', 'Kasoma', '', 'Recast Perspectives - Creative media innovation and fresh perspectives in journalism.'),
(6, 'Mwebaza', 'Phillip', '', 'Smarty - Innovation in media technology and digital content solutions.'),
(6, 'Serina', 'Nagujja', '', 'NSTV - Excellence in media innovation and digital broadcasting.');

-- =====================================================
-- STEP 4: UPDATE ADMIN CREDENTIALS
-- =====================================================

UPDATE `admin` SET
    `firstname` = 'Media Challenge',
    `lastname` = 'Initiative',
    `username` = 'admin'
WHERE `id` = 1;

-- =====================================================
-- STEP 5: ADD PERFORMANCE INDEXES
-- =====================================================

-- Drop existing indexes if they exist (to avoid errors)
DROP INDEX IF EXISTS `idx_position` ON `candidates`;
DROP INDEX IF EXISTS `idx_voters` ON `votes`;
DROP INDEX IF EXISTS `idx_candidate` ON `votes`;
DROP INDEX IF EXISTS `idx_position_votes` ON `votes`;
DROP INDEX IF EXISTS `idx_priority` ON `positions`;

-- Create optimized indexes
CREATE INDEX `idx_position` ON `candidates` (`position_id`);
CREATE INDEX `idx_voters` ON `votes` (`voters_id`);
CREATE INDEX `idx_candidate` ON `votes` (`candidate_id`);
CREATE INDEX `idx_position_votes` ON `votes` (`position_id`);
CREATE INDEX `idx_priority` ON `positions` (`priority`);

-- Composite index for common query patterns
CREATE INDEX `idx_votes_voter_position` ON `votes` (`voters_id`, `position_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =====================================================
-- VERIFICATION QUERIES (Optional - Run to verify data)
-- =====================================================
-- SELECT 'Award Categories' as Info, COUNT(*) as Count FROM positions;
-- SELECT 'Total Nominees' as Info, COUNT(*) as Count FROM candidates;
-- SELECT p.description as Category, COUNT(c.id) as Nominees
--   FROM positions p
--   LEFT JOIN candidates c ON p.id = c.position_id
--   GROUP BY p.id
--   ORDER BY p.priority;

-- =====================================================
-- SAMPLE VOTER (for testing - password is 'password')
-- =====================================================
-- INSERT INTO `voters` (`voters_id`, `password`, `firstname`, `lastname`, `photo`) VALUES
-- ('TEST001', '$2y$10$fLK8s7ZDnM.1lE7XMP.J6OuPbQ.DPUVKBo7rENnQY7gYq0xAzsKJy', 'Test', 'Voter', '');
