<?php
/**
 * The game object
 *
 * PHP version 5
 *
 * @category Game
 * @package  FOGProject
 * @author   Your Name <your.email@example.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * The game object
 *
 * @category Game
 * @package  FOGProject
 * @author   Your Name <your.email@example.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class Game extends FOGController
{
    /**
     * The game table
     *
     * @var string
     */
    protected $databaseTable = 'games';
    
    /**
     * The Game table fields and common names
     *
     * @var array
     */
    protected $databaseFields = array(
        'id' => 'gameID',
        'name' => 'gameName',
        'description' => 'gameDesc',
        'icon' => 'gameIcon',
        'downloadPath' => 'gameDownloadPath',
        'executable' => 'gameExecutable',
        'parameters' => 'gameParameters',
        'archivePath' => 'gameArchivePath',
        'syncServer' => 'gameSyncServer',
        'driveLetter' => 'gameDriveLetter',
        'size' => 'gameSize',
        'state' => 'gameState',
        'lastUpdate' => 'gameLastUpdate',
        'localUpdateTime' => 'gameLocalUpdateTime',
        'runCount' => 'gameRunCount',
        'lastRunTime' => 'gameLastRunTime',
        'createdTime' => 'gameDateTime',
        'createdBy' => 'gameCreateBy',
        'protected' => 'gameProtect',
        'isEnabled' => 'gameEnabled',
        'autoUpdate' => 'gameAutoUpdate',
    );
    
    /**
     * The required fields
     *
     * @var array
     */
    protected $databaseFieldsRequired = array(
        'name',
        'downloadPath',
    );
    
    /**
     * Additional fields
     *
     * @var array
     */
    protected $additionalFields = array(
        'hosts',
        'hostsnotinme',
    );
    
    /**
     * Removes the item from the database
     *
     * @param string $key the key to remove
     *
     * @throws Exception
     * @return object
     */
    public function destroy($key = 'id')
    {
        self::$HookManager
            ->processEvent(
                'DESTROY_GAME',
                array(
                    'Game' => &$this
                )
            );
        return parent::destroy($key);
    }
    
    /**
     * Stores data into the database
     *
     * @return bool|object
     */
    public function save()
    {
        parent::save();
        return $this;
    }
    
    /**
     * Get the game size formatted
     *
     * @return string
     */
    public function getSize()
    {
        return self::formatByteSize($this->get('size'));
    }
    
    /**
     * Get the game state display
     *
     * @return string
     */
    public function getStateDisplay()
    {
        $states = array(
            0 => _('Not Downloaded'),
            1 => _('Downloading'),
            2 => _('Downloaded'),
            3 => _('Installing'),
            4 => _('Installed'),
            5 => _('Updating'),
            6 => _('Error'),
        );
        return isset($states[$this->get('state')]) 
            ? $states[$this->get('state')] 
            : _('Unknown');
    }
    
    /**
     * Load the game from path
     *
     * @param string $path the path to check
     *
     * @return object
     */
    public function loadPath($path)
    {
        return $this->load(
            array(
                'downloadPath' => $path
            )
        );
    }
    
    /**
     * Check if game exists by name
     *
     * @param string $name the name to check
     *
     * @return bool
     */
    public function exists($name)
    {
        return self::getClass('GameManager')
            ->exists($name, '', 'name');
    }
}
