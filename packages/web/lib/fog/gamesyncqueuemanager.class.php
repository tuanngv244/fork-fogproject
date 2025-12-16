<?php
/**
 * Game sync queue manager class
 *
 * PHP version 5
 *
 * @category GameSyncQueueManager
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game sync queue manager class
 *
 * Manages the game sync queue, handles scheduling and task creation.
 *
 * @category GameSyncQueueManager
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameSyncQueueManager extends FOGManagerController
{
    /**
     * The base table name
     *
     * @var string
     */
    public $tablename = 'game_sync_queue';
    /**
     * Install our table
     *
     * @return bool
     */
    public function install()
    {
        $this->uninstall();
        $sql = Schema::createTable(
            $this->tablename,
            true,
            array(
                'gsqID',
                'gsqGameID',
                'gsqHostID',
                'gsqPriority',
                'gsqRequestedBy',
                'gsqRequestedTime',
                'gsqScheduledTime',
                'gsqStatus',
                'gsqTaskID',
                'gsqCompletedTime',
                'gsqErrorMessage',
                'gsqMetadata'
            ),
            array(
                'INTEGER',
                'INTEGER',
                'INTEGER',
                'INTEGER',
                'VARCHAR(100)',
                'DATETIME',
                'DATETIME',
                "ENUM('pending','queued','processing','completed','failed')",
                'INTEGER',
                'DATETIME',
                'TEXT',
                'TEXT'
            ),
            array(
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false
            ),
            false,
            'MyISAM',
            'utf8mb4',
            'gsqID',
            'gsqID'
        );
        return self::$DB->query($sql);
    }
    /**
     * Queue a game sync for a host
     *
     * @param int    $gameID      The game to sync
     * @param int    $hostID      The target host
     * @param int    $priority    Priority (higher = more urgent)
     * @param string $requestedBy Who/what requested this sync
     * @param array  $metadata    Optional metadata
     *
     * @return object The created GameSyncQueue entry
     */
    public function queueSync(
        $gameID,
        $hostID,
        $priority = 5,
        $requestedBy = 'manual',
        $metadata = array()
    ) {
        // Check if already queued
        $existing = self::getClass('GameSyncQueueManager')
            ->find(
                array(
                    'gameID' => $gameID,
                    'hostID' => $hostID,
                    'status' => array('pending', 'queued', 'processing')
                )
            );
        if (count($existing) > 0) {
            throw new Exception(
                sprintf(
                    'Game %d already queued for host %d',
                    $gameID,
                    $hostID
                )
            );
        }
        $queue = self::getClass('GameSyncQueue')
            ->set('gameID', $gameID)
            ->set('hostID', $hostID)
            ->set('priority', $priority)
            ->set('requestedBy', $requestedBy)
            ->set('requestedTime', self::niceDate()->format('Y-m-d H:i:s'))
            ->set('status', 'pending');
        if (!empty($metadata)) {
            $queue->setMetadata($metadata);
        }
        if (!$queue->save()) {
            throw new Exception('Failed to queue game sync');
        }
        self::getClass('EventManager')
            ->notify(
                'GAME_SYNC_QUEUED',
                array(
                    'GameSyncQueue' => &$queue
                )
            );
        return $queue;
    }
    /**
     * Get pending sync requests ready for processing
     *
     * @param int $limit Maximum syncs to return
     *
     * @return array
     */
    public function getPendingSyncs($limit = 10)
    {
        return self::getClass('GameSyncQueueManager')
            ->find(
                array('status' => 'pending'),
                'OR',
                'priority',
                'DESC',
                '',
                '',
                $limit
            );
    }
    /**
     * Get queued syncs for a specific host
     *
     * @param int $hostID The host ID
     *
     * @return array
     */
    public function getQueuedSyncsForHost($hostID)
    {
        return self::getClass('GameSyncQueueManager')
            ->find(
                array(
                    'hostID' => $hostID,
                    'status' => array('pending', 'queued', 'processing')
                )
            );
    }
    /**
     * Get all queued syncs for a game
     *
     * @param int $gameID The game ID
     *
     * @return array
     */
    public function getQueuedSyncsForGame($gameID)
    {
        return self::getClass('GameSyncQueueManager')
            ->find(
                array(
                    'gameID' => $gameID,
                    'status' => array('pending', 'queued', 'processing')
                )
            );
    }
    /**
     * Get failed syncs for retry
     *
     * @param int $hours Look back this many hours
     *
     * @return array
     */
    public function getRecentFailures($hours = 24)
    {
        $since = self::niceDate()
            ->modify(sprintf('-%d hours', $hours))
            ->format('Y-m-d H:i:s');
        return self::getClass('GameSyncQueueManager')
            ->find(
                array(
                    'status' => 'failed',
                    'completedTime' => array('op' => '>=', 'value' => $since)
                )
            );
    }
    /**
     * Get queue statistics
     *
     * @return array
     */
    public function getQueueStats()
    {
        $stats = array(
            'pending' => 0,
            'queued' => 0,
            'processing' => 0,
            'completed_24h' => 0,
            'failed_24h' => 0,
            'average_priority' => 0
        );
        // Count by status
        foreach (array('pending', 'queued', 'processing') as $status) {
            $stats[$status] = self::getClass('GameSyncQueueManager')
                ->count(array('status' => $status));
        }
        // Recent completions and failures
        $since = self::niceDate()
            ->modify('-24 hours')
            ->format('Y-m-d H:i:s');
        $stats['completed_24h'] = self::getClass('GameSyncQueueManager')
            ->count(
                array(
                    'status' => 'completed',
                    'completedTime' => array('op' => '>=', 'value' => $since)
                )
            );
        $stats['failed_24h'] = self::getClass('GameSyncQueueManager')
            ->count(
                array(
                    'status' => 'failed',
                    'completedTime' => array('op' => '>=', 'value' => $since)
                )
            );
        // Average priority of pending syncs
        $pending = self::getClass('GameSyncQueueManager')
            ->find(array('status' => 'pending'));
        if (count($pending) > 0) {
            $total = 0;
            foreach ($pending as $item) {
                $total += $item->get('priority');
            }
            $stats['average_priority'] = round($total / count($pending), 2);
        }
        return $stats;
    }
}
