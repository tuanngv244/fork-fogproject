-- FOG Game Module - Quick Install with Sample Data
-- This includes table creation and 5 sample games

-- Create table
CREATE TABLE IF NOT EXISTS `games` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `gameName` varchar(255) NOT NULL,
  `gameDescription` text,
  `gameIcon` varchar(500) DEFAULT NULL,
  `gameDownloadPath` varchar(1000) DEFAULT NULL,
  `gameExecutable` varchar(500) DEFAULT NULL,
  `gameParameters` varchar(1000) DEFAULT NULL,
  `gameArchivePath` varchar(1000) DEFAULT NULL,
  `gameSyncServer` int(11) DEFAULT '0',
  `gameDriveLetter` varchar(3) DEFAULT 'C:',
  `gameSize` bigint(20) DEFAULT '0',
  `gameState` int(2) DEFAULT '0' COMMENT '0=NotDownloaded,1=Downloading,2=Ready,3=Installing,4=Installed,5=Updating,6=Protected',
  `gameLastUpdate` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gameRunCount` int(11) DEFAULT '0',
  `gameVersion` varchar(50) DEFAULT NULL,
  `gamePublisher` varchar(255) DEFAULT NULL,
  `gameGenre` varchar(100) DEFAULT NULL,
  `gameRating` varchar(10) DEFAULT NULL,
  `gameReleaseDate` date DEFAULT NULL,
  `gameRequirements` text,
  `gameNotes` text,
  PRIMARY KEY (`gameID`),
  KEY `idx_state` (`gameState`),
  KEY `idx_name` (`gameName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Insert sample data
INSERT INTO `games` (`gameName`, `gameDescription`, `gameIcon`, `gameDownloadPath`, `gameExecutable`, `gameParameters`, `gameArchivePath`, `gameSyncServer`, `gameDriveLetter`, `gameSize`, `gameState`, `gameVersion`, `gamePublisher`, `gameGenre`) VALUES
('League of Legends', 'MOBA game - 5v5 team battles', 'https://example.com/lol.png', '\\\\192.168.1.100\\games\\LOL', 'LeagueClient.exe', '--launch-product=league_of_legends', '\\\\192.168.1.100\\archives\\LOL.zip', 1, 'D:', 85899345920, 2, '14.1', 'Riot Games', 'MOBA'),
('Counter-Strike 2', 'Tactical FPS shooter', 'https://example.com/cs2.png', '\\\\192.168.1.100\\games\\CS2', 'cs2.exe', '-fullscreen', '\\\\192.168.1.100\\archives\\CS2.zip', 1, 'D:', 34359738368, 4, '1.0.2', 'Valve', 'FPS'),
('Dota 2', 'Multiplayer online battle arena', 'https://example.com/dota2.png', '\\\\192.168.1.100\\games\\Dota2', 'dota2.exe', '', '\\\\192.168.1.100\\archives\\Dota2.zip', 1, 'E:', 47244640256, 2, '7.35c', 'Valve', 'MOBA'),
('Valorant', 'Tactical shooter with abilities', 'https://example.com/valorant.png', '\\\\192.168.1.100\\games\\Valorant', 'VALORANT.exe', '', '\\\\192.168.1.100\\archives\\Valorant.zip', 2, 'D:', 0, 0, '8.0', 'Riot Games', 'FPS'),
('GTA V', 'Open world action-adventure', 'https://example.com/gtav.png', '\\\\192.168.1.100\\games\\GTAV', 'GTA5.exe', '', '\\\\192.168.1.100\\archives\\GTAV.zip', 1, 'D:', 107374182400, 6, '1.67', 'Rockstar Games', 'Action');

SELECT 'Game module installed successfully!' as Status;
SELECT COUNT(*) as TotalGames FROM games;
