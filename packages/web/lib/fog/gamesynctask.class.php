<?php
/**
 * Game sync task metadata class
 *
 * PHP version 5
 *
 * @category GameSyncTask
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game sync task metadata class
 *
 * Stores additional metadata for game sync tasks that doesn't fit
 * in the core tasks table. Links to parent Task via taskID.
 *
 * @category GameSyncTask
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameSyncTask extends FOGController
{
    /**
     * The game sync task table
     *
     * @var string
     */
    protected $databaseTable = 'game_sync_tasks';
    /**
     * The GameSyncTask table fields and common names
     *
     * @var array
     */
    protected $databaseFields = array(
        'id' => 'gstID',
        'taskID' => 'gstTaskID',
        'gameID' => 'gstGameID',
        'hostID' => 'gstHostID',
        'syncMethod' => 'gstSyncMethod',
        'sourcePath' => 'gstSourcePath',
        'destinationPath' => 'gstDestinationPath',
        'fileList' => 'gstFileList',
        'totalFiles' => 'gstTotalFiles',
        'filesProcessed' => 'gstFilesProcessed',
        'bytesTotal' => 'gstBytesTotal',
        'bytesTransferred' => 'gstBytesTransferred',
        'startTime' => 'gstStartTime',
        'endTime' => 'gstEndTime',
        'errorMessage' => 'gstErrorMessage',
        'retryCount' => 'gstRetryCount',
        'lastRetry' => 'gstLastRetry',
        'metadata' => 'gstMetadata',
    );
    /**
     * The required fields
     *
     * @var array
     */
    protected $databaseFieldsRequired = array(
        'taskID',
        'gameID',
        'hostID',
    );
    /**
     * Additional fields
     *
     * @var array
     */
    protected $additionalFields = array(
        'task',
        'game',
        'host',
    );
    /**
     * Database -> Class field relationships
     *
     * @var array
     */
    protected $databaseFieldClassRelationships = array(
        'Task' => array(
            'id',
            'taskID',
            'task'
        ),
        'Game' => array(
            'id',
            'gameID',
            'game'
        ),
        'Host' => array(
            'id',
            'hostID',
            'host'
        )
    );
    /**
     * Get the parent task
     *
     * @return object
     */
    public function getTask()
    {
        return $this->get('task');
    }
    /**
     * Get the game being synced
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
    /**
     * Calculate sync progress percentage
     *
     * @return float
     */
    public function getProgress()
    {
        $total = (float)$this->get('bytesTotal');
        if ($total <= 0) {
            return 0.0;
        }
        $transferred = (float)$this->get('bytesTransferred');
        return min(100.0, ($transferred / $total) * 100.0);
    }
    /**
     * Check if sync is complete
     *
     * @return bool
     */
    public function isComplete()
    {
        $endTime = $this->get('endTime');
        return !empty($endTime) && self::validDate($endTime);
    }
    /**
     * Check if sync is in progress
     *
     * @return bool
     */
    public function isInProgress()
    {
        $startTime = $this->get('startTime');
        $endTime = $this->get('endTime');
        return self::validDate($startTime) && empty($endTime);
    }
    /**
     * Check if sync failed
     *
     * @return bool
     */
    public function hasFailed()
    {
        return !empty($this->get('errorMessage'));
    }
}
