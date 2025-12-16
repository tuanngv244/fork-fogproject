<?php
/**
 * Game sync task manager class
 *
 * PHP version 5
 *
 * @category GameSyncTaskManager
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game sync task manager class
 *
 * @category GameSyncTaskManager
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameSyncTaskManager extends FOGManagerController
{
    /**
     * Get active sync tasks for a host
     *
     * @param int $hostID The host ID
     *
     * @return array
     */
    public function getActiveSyncsForHost($hostID)
    {
        return $this->find(
            array(
                'hostID' => $hostID,
                'endTime' => array('', null)
            )
        );
    }
    /**
     * Get all sync tasks for a game
     *
     * @param int $gameID The game ID
     *
     * @return array
     */
    public function getSyncsForGame($gameID)
    {
        return $this->find(array('gameID' => $gameID));
    }
    /**
     * Get failed syncs that can be retried
     *
     * @param int $maxRetries Maximum retry count
     *
     * @return array
     */
    public function getFailedSyncs($maxRetries = 3)
    {
        $syncs = $this->find();
        $failed = array();
        foreach ((array)$syncs as &$sync) {
            if ($sync->hasFailed() 
                && $sync->get('retryCount') < $maxRetries
                && !$sync->isComplete()
            ) {
                $failed[] = $sync;
            }
            unset($sync);
        }
        return $failed;
    }
    /**
     * Get sync statistics for a game
     *
     * @param int $gameID The game ID
     *
     * @return array
     */
    public function getGameSyncStats($gameID)
    {
        $syncs = $this->getSyncsForGame($gameID);
        $stats = array(
            'total' => count($syncs),
            'completed' => 0,
            'in_progress' => 0,
            'failed' => 0,
            'total_bytes' => 0,
            'transferred_bytes' => 0
        );
        foreach ((array)$syncs as &$sync) {
            if ($sync->isComplete()) {
                $stats['completed']++;
            } elseif ($sync->isInProgress()) {
                $stats['in_progress']++;
            } elseif ($sync->hasFailed()) {
                $stats['failed']++;
            }
            $stats['total_bytes'] += $sync->get('bytesTotal');
            $stats['transferred_bytes'] += $sync->get('bytesTransferred');
            unset($sync);
        }
        return $stats;
    }
}
