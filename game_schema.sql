-- FOG Game Module - Database Schema
-- Run this to create games table

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
