-- ============================================
-- FOG Game Management Module - Database Schema
-- ============================================
-- Tạo bảng games
-- ============================================

CREATE TABLE IF NOT EXISTS `games` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `gameName` varchar(255) NOT NULL,
  `gameDesc` longtext,
  `gameIcon` longtext,
  `gameDownloadPath` longtext NOT NULL,
  `gameExecutable` varchar(255) DEFAULT NULL,
  `gameParameters` longtext,
  `gameArchivePath` longtext,
  `gameSyncServer` varchar(100) DEFAULT NULL,
  `gameDriveLetter` varchar(10) DEFAULT NULL,
  `gameSize` bigint(20) DEFAULT '0',
  `gameState` int(11) DEFAULT '0' COMMENT '0=Not Downloaded, 1=Downloading, 2=Downloaded, 3=Installing, 4=Installed, 5=Updating, 6=Error',
  `gameLastUpdate` datetime DEFAULT NULL,
  `gameLocalUpdateTime` datetime DEFAULT NULL,
  `gameRunCount` int(11) DEFAULT '0',
  `gameLastRunTime` datetime DEFAULT NULL,
  `gameDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `gameCreateBy` varchar(40) DEFAULT NULL,
  `gameProtect` enum('0','1') NOT NULL DEFAULT '0',
  `gameEnabled` enum('0','1') NOT NULL DEFAULT '1',
  `gameAutoUpdate` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameID`),
  KEY `gameName` (`gameName`),
  KEY `gameState` (`gameState`),
  KEY `gameEnabled` (`gameEnabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Game Management Table';

-- ============================================
-- Sample Data (Optional)
-- ============================================

INSERT INTO `games` (`gameName`, `gameDesc`, `gameDownloadPath`, `gameExecutable`, `gameState`, `gameEnabled`) VALUES
('League of Legends', 'MOBA game by Riot Games', 'D:\\Games\\LOL', 'LeagueClient.exe', 2, '1'),
('Counter-Strike 2', 'Tactical FPS by Valve', 'D:\\Games\\CS2', 'cs2.exe', 2, '1'),
('Dota 2', 'MOBA game by Valve', 'D:\\Games\\Dota2', 'dota2.exe', 1, '1');

-- ============================================
-- Indexes for performance
-- ============================================

ALTER TABLE `games` ADD INDEX `idx_game_search` (`gameName`, `gameEnabled`, `gameState`);
ALTER TABLE `games` ADD INDEX `idx_game_sync` (`gameSyncServer`, `gameAutoUpdate`);
