-- 
-- Game Sync Task Type Schema
-- Extends FOG task infrastructure for game synchronization
--

--
-- Add GameSync task type (ID 25)
-- Type defines WHAT to sync, not HOW
--
INSERT INTO `taskTypes` (
    `ttID`,
    `ttName`,
    `ttDescription`,
    `ttIcon`,
    `ttKernel`,
    `ttKernelArgs`,
    `ttType`,
    `ttIsAdvanced`,
    `ttIsAccess`,
    `ttInitrd`
) VALUES (
    25,
    'Game Sync',
    'Synchronizes game files from storage to host based on game assignments',
    'gamepad',
    'fog/kernel/bzImage',
    '',
    'game_sync',
    0,
    1,
    'fog/images/init.xz'
) ON DUPLICATE KEY UPDATE
    `ttName` = VALUES(`ttName`),
    `ttDescription` = VALUES(`ttDescription`),
    `ttIcon` = VALUES(`ttIcon`),
    `ttType` = VALUES(`ttType`);

--
-- Extend tasks table with gameID field
-- Links task to specific game being synced
--
ALTER TABLE `tasks`
    ADD COLUMN `taskGameID` int(11) NOT NULL DEFAULT '0' AFTER `taskImageID`,
    ADD INDEX `idx_game` (`taskGameID`);

--
-- Create game_sync_tasks table for tracking sync-specific metadata
-- Stores additional context that doesn't fit in core tasks table
--
CREATE TABLE IF NOT EXISTS `game_sync_tasks` (
    `gstID` int(11) NOT NULL AUTO_INCREMENT,
    `gstTaskID` int(11) NOT NULL,
    `gstGameID` int(11) NOT NULL,
    `gstHostID` int(11) NOT NULL,
    `gstSyncMethod` varchar(50) DEFAULT 'rsync',
    `gstSourcePath` text,
    `gstDestinationPath` text,
    `gstFileList` longtext,
    `gstTotalFiles` int(11) DEFAULT 0,
    `gstFilesProcessed` int(11) DEFAULT 0,
    `gstBytesTotal` bigint(20) DEFAULT 0,
    `gstBytesTransferred` bigint(20) DEFAULT 0,
    `gstStartTime` datetime DEFAULT NULL,
    `gstEndTime` datetime DEFAULT NULL,
    `gstErrorMessage` text,
    `gstRetryCount` int(11) DEFAULT 0,
    `gstLastRetry` datetime DEFAULT NULL,
    `gstMetadata` text COMMENT 'JSON metadata for extensibility',
    PRIMARY KEY (`gstID`),
    UNIQUE KEY `unique_task` (`gstTaskID`),
    KEY `idx_game` (`gstGameID`),
    KEY `idx_host` (`gstHostID`),
    KEY `idx_status` (`gstStartTime`, `gstEndTime`),
    CONSTRAINT `fk_gst_task` FOREIGN KEY (`gstTaskID`) REFERENCES `tasks` (`taskID`) ON DELETE CASCADE,
    CONSTRAINT `fk_gst_game` FOREIGN KEY (`gstGameID`) REFERENCES `games` (`gameID`) ON DELETE CASCADE,
    CONSTRAINT `fk_gst_host` FOREIGN KEY (`gstHostID`) REFERENCES `hosts` (`hostID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Create game_sync_queue table for scheduling sync operations
-- Decouples sync requests from task creation for better control
--
CREATE TABLE IF NOT EXISTS `game_sync_queue` (
    `gsqID` int(11) NOT NULL AUTO_INCREMENT,
    `gsqGameID` int(11) NOT NULL,
    `gsqHostID` int(11) NOT NULL,
    `gsqPriority` int(11) DEFAULT 5 COMMENT 'Lower number = higher priority',
    `gsqRequestedBy` varchar(255) DEFAULT NULL,
    `gsqRequestedTime` datetime NOT NULL,
    `gsqScheduledTime` datetime DEFAULT NULL,
    `gsqStatus` enum('pending','queued','processing','completed','failed','cancelled') DEFAULT 'pending',
    `gsqTaskID` int(11) DEFAULT NULL COMMENT 'Set when task is created',
    `gsqCompletedTime` datetime DEFAULT NULL,
    `gsqErrorMessage` text,
    `gsqMetadata` text COMMENT 'JSON for additional context',
    PRIMARY KEY (`gsqID`),
    KEY `idx_game` (`gsqGameID`),
    KEY `idx_host` (`gsqHostID`),
    KEY `idx_status_priority` (`gsqStatus`, `gsqPriority`, `gsqScheduledTime`),
    KEY `idx_task` (`gsqTaskID`),
    CONSTRAINT `fk_gsq_game` FOREIGN KEY (`gsqGameID`) REFERENCES `games` (`gameID`) ON DELETE CASCADE,
    CONSTRAINT `fk_gsq_host` FOREIGN KEY (`gsqHostID`) REFERENCES `hosts` (`hostID`) ON DELETE CASCADE,
    CONSTRAINT `fk_gsq_task` FOREIGN KEY (`gsqTaskID`) REFERENCES `tasks` (`taskID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Create indexes for efficient querying
--
ALTER TABLE `game_sync_queue`
    ADD INDEX `idx_pending_tasks` (`gsqStatus`, `gsqScheduledTime`, `gsqPriority`);

ALTER TABLE `game_sync_tasks`
    ADD INDEX `idx_active_syncs` (`gstStartTime`, `gstEndTime`);
