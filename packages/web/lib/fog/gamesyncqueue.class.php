<?php
/**
 * Game sync queue class
 *
 * PHP version 5
 *
 * @category GameSyncQueue
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game sync queue class
 *
 * Represents a queued game sync request. Decouples sync requests
 * from actual task creation, allowing for scheduling and prioritization.
 *
 * @category GameSyncQueue
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameSyncQueue extends FOGController
{
    /**
     * The game sync queue table
     *
     * @var string
     */
    protected $databaseTable = 'game_sync_queue';
    /**
     * The GameSyncQueue table fields and common names
     *
     * @var array
     */
    protected $databaseFields = array(
        'id' => 'gsqID',
        'gameID' => 'gsqGameID',
        'hostID' => 'gsqHostID',
        'priority' => 'gsqPriority',
        'requestedBy' => 'gsqRequestedBy',
        'requestedTime' => 'gsqRequestedTime',
        'scheduledTime' => 'gsqScheduledTime',
        'status' => 'gsqStatus',
        'taskID' => 'gsqTaskID',
        'completedTime' => 'gsqCompletedTime',
        'errorMessage' => 'gsqErrorMessage',
        'metadata' => 'gsqMetadata',
    );
    /**
     * The required fields
     *
     * @var array
     */
    protected $databaseFieldsRequired = array(
        'gameID',
        'hostID',
        'requestedTime',
    );
    /**
     * Additional fields
     *
     * @var array
     */
    protected $additionalFields = array(
        'game',
        'host',
        'task',
    );
    /**
     * Database -> Class field relationships
     *
     * @var array
     */
    protected $databaseFieldClassRelationships = array(
        'Game' => array(
            'id',
            'gameID',
            'game'
        ),
        'Host' => array(
            'id',
            'hostID',
            'host'
        ),
        'Task' => array(
            'id',
            'taskID',
            'task'
        )
    );
    /**
     * Get the game to sync
     *
     * @return object
     */
    public function getGame()
    {
        return $this->get('game');
    }
    /**
     * Get the target host
     *
     * @return object
     */
    public function getHost()
    {
        return $this->get('host');
    }
    /**
     * Get the associated task if created
     *
     * @return object|null
     */
    public function getTask()
    {
        if (!$this->get('taskID')) {
            return null;
        }
        return $this->get('task');
    }
    /**
     * Check if this sync is pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->get('status') === 'pending';
    }
    /**
     * Check if this sync is queued
     *
     * @return bool
     */
    public function isQueued()
    {
        return $this->get('status') === 'queued';
    }
    /**
     * Check if this sync is processing
     *
     * @return bool
     */
    public function isProcessing()
    {
        return $this->get('status') === 'processing';
    }
    /**
     * Check if this sync is completed
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->get('status') === 'completed';
    }
    /**
     * Check if this sync failed
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->get('status') === 'failed';
    }
    /**
     * Mark as queued
     *
     * @return object
     */
    public function markQueued()
    {
        return $this->set('status', 'queued');
    }
    /**
     * Mark as processing and link to task
     *
     * @param int $taskID The created task ID
     *
     * @return object
     */
    public function markProcessing($taskID)
    {
        return $this
            ->set('status', 'processing')
            ->set('taskID', $taskID);
    }
    /**
     * Mark as completed
     *
     * @return object
     */
    public function markCompleted()
    {
        return $this
            ->set('status', 'completed')
            ->set('completedTime', self::niceDate()->format('Y-m-d H:i:s'));
    }
    /**
     * Mark as failed
     *
     * @param string $error The error message
     *
     * @return object
     */
    public function markFailed($error)
    {
        return $this
            ->set('status', 'failed')
            ->set('errorMessage', $error)
            ->set('completedTime', self::niceDate()->format('Y-m-d H:i:s'));
    }
    /**
     * Get metadata as associative array
     *
     * @return array
     */
    public function getMetadata()
    {
        $json = $this->get('metadata');
        if (empty($json)) {
            return array();
        }
        $data = json_decode($json, true);
        return is_array($data) ? $data : array();
    }
    /**
     * Set metadata from associative array
     *
     * @param array $data The metadata to store
     *
     * @return object
     */
    public function setMetadata($data)
    {
        $json = json_encode($data);
        return $this->set('metadata', $json);
    }
}
