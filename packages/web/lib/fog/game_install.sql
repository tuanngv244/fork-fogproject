/**
 * FOG Game Management Module - Quick Setup Script
 * Run this in MySQL to quickly setup the game module
 */

-- Step 1: Create games table
USE fog;

DROP TABLE IF EXISTS `games`;

CREATE TABLE `games` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `gameName` varchar(255) NOT NULL,
  `gameDesc` longtext,
  `gameIcon` longtext,
  `gameDownloadPath` longtext NOT NULL,
  `gameExecutable` varchar(255) DEFAULT NULL,
  `gameParameters` longtext,
  `gameArchivePath` longtext,
  `gameSyncServer` varchar(100) DEFAULT NULL,
  `gameDriveLetter` varchar(10) DEFAULT 'D:',
  `gameSize` bigint(20) DEFAULT '0',
  `gameState` int(11) DEFAULT '0',
  `gameLastUpdate` datetime DEFAULT NULL,
  `gameLocalUpdateTime` datetime DEFAULT NULL,
  `gameRunCount` int(11) DEFAULT '0',
  `gameLastRunTime` datetime DEFAULT NULL,
  `gameDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gameCreateBy` varchar(40) DEFAULT 'fog',
  `gameProtect` enum('0','1') NOT NULL DEFAULT '0',
  `gameEnabled` enum('0','1') NOT NULL DEFAULT '1',
  `gameAutoUpdate` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameID`),
  UNIQUE KEY `gameName` (`gameName`),
  KEY `idx_game_state` (`gameState`),
  KEY `idx_game_enabled` (`gameEnabled`),
  KEY `idx_game_search` (`gameName`,`gameEnabled`,`gameState`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='FOG Game Management';

-- Step 2: Insert demo data
INSERT INTO `games` VALUES 
(1, 'League of Legends', 'Multiplayer Online Battle Arena game developed by Riot Games', 
 'https://example.com/icons/lol.png', 
 'D:\\Games\\LeagueOfLegends', 'LeagueClient.exe', '--locale=en_US', 
 'D:\\GameArchive\\LOL', 'VANTUAN', 'D:', 85899345920, 2, 
 '2025-12-15 10:30:00', '2025-12-15 10:30:00', 150, '2025-12-16 08:20:00', 
 CURRENT_TIMESTAMP, 'admin', '0', '1', '1'),

(2, 'Counter-Strike 2', 'Tactical first-person shooter by Valve Corporation', 
 'https://example.com/icons/cs2.png', 
 'D:\\Games\\CounterStrike2', 'cs2.exe', '-high -novid', 
 'D:\\GameArchive\\CS2', 'VANTUAN', 'D:', 34359738368, 2, 
 '2025-12-14 15:45:00', '2025-12-14 15:45:00', 89, '2025-12-16 07:15:00', 
 CURRENT_TIMESTAMP, 'admin', '0', '1', '1'),

(3, 'Dota 2', 'Multiplayer online battle arena video game', 
 'https://example.com/icons/dota2.png', 
 'D:\\Games\\Dota2', 'dota2.exe', '-perfectworld', 
 'D:\\GameArchive\\Dota2', 'VANTUAN', 'D:', 47244640256, 1, 
 NULL, NULL, 0, NULL, 
 CURRENT_TIMESTAMP, 'admin', '0', '1', '0'),

(4, 'Valorant', 'Free-to-play first-person tactical hero shooter', 
 'https://example.com/icons/valorant.png', 
 'D:\\Games\\Valorant', 'VALORANT.exe', '', 
 'D:\\GameArchive\\Valorant', 'VANTUAN', 'E:', 0, 0, 
 NULL, NULL, 0, NULL, 
 CURRENT_TIMESTAMP, 'admin', '0', '1', '0'),

(5, 'GTA V', 'Action-adventure game by Rockstar Games', 
 'https://example.com/icons/gtav.png', 
 'D:\\Games\\GTAV', 'GTA5.exe', '-fullscreen', 
 'D:\\GameArchive\\GTAV', 'VANTUAN', 'E:', 107374182400, 2, 
 '2025-12-10 20:00:00', '2025-12-10 20:00:00', 45, '2025-12-15 19:30:00', 
 CURRENT_TIMESTAMP, 'admin', '1', '1', '0');

-- Step 3: Verify installation
SELECT 
    COUNT(*) as total_games,
    SUM(CASE WHEN gameState = 2 THEN 1 ELSE 0 END) as downloaded_games,
    SUM(CASE WHEN gameState = 1 THEN 1 ELSE 0 END) as downloading_games,
    SUM(gameSize) as total_size_bytes,
    ROUND(SUM(gameSize) / 1024 / 1024 / 1024, 2) as total_size_gb
FROM games;

-- Show sample data
SELECT 
    gameID,
    gameName,
    CASE gameState
        WHEN 0 THEN 'Not Downloaded'
        WHEN 1 THEN 'Downloading'
        WHEN 2 THEN 'Downloaded'
        WHEN 3 THEN 'Installing'
        WHEN 4 THEN 'Installed'
        WHEN 5 THEN 'Updating'
        WHEN 6 THEN 'Error'
    END as state,
    ROUND(gameSize / 1024 / 1024 / 1024, 2) as size_gb,
    gameEnabled as enabled
FROM games
ORDER BY gameID;

-- Success message
SELECT 'Game Management Module installed successfully!' as message;
