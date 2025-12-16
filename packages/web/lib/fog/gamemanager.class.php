<?php
/**
 * Game manager mass management class
 *
 * PHP version 5
 *
 * @category GameManager
 * @package  FOGProject
 * @author   Your Name <your.email@example.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game manager mass management class
 *
 * @category GameManager
 * @package  FOGProject
 * @author   Your Name <your.email@example.com>
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameManager extends FOGManagerController
{
    /**
     * The base table name.
     *
     * @var string
     */
    public $tablename = 'games';
    
    /**
     * Install our table.
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
                'gameID',
                'gameName',
                'gameDesc',
                'gameIcon',
                'gameDownloadPath',
                'gameExecutable',
                'gameParameters',
                'gameArchivePath',
                'gameSyncServer',
                'gameDriveLetter',
                'gameSize',
                'gameState',
                'gameLastUpdate',
                'gameLocalUpdateTime',
                'gameRunCount',
                'gameLastRunTime',
                'gameDateTime',
                'gameCreateBy',
                'gameProtect',
                'gameEnabled',
                'gameAutoUpdate',
            ),
            array(
                'INTEGER',
                'VARCHAR(255)',
                'LONGTEXT',
                'LONGTEXT',
                'LONGTEXT',
                'VARCHAR(255)',
                'LONGTEXT',
                'LONGTEXT',
                'VARCHAR(100)',
                'VARCHAR(10)',
                'BIGINT',
                'INTEGER',
                'DATETIME',
                'DATETIME',
                'INTEGER',
                'DATETIME',
                'TIMESTAMP',
                'VARCHAR(40)',
                "ENUM('0', '1')",
                "ENUM('0', '1')",
                "ENUM('0', '1')",
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
                false,
                false,
                false,
                false,
                false,
                'CURRENT_TIMESTAMP',
                false,
                '0',
                '1',
                '0',
            ),
            array(
                'PRIMARY',
                array('gameName'),
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
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
            ),
            'MyISAM',
            'utf8',
            'gameID',
            'gameID'
        );
        
        return self::$DB->query($sql);
    }
    
    /**
     * Get total games size
     *
     * @return int
     */
    public function getTotalSize()
    {
        $total = 0;
        foreach ((array)$this->find() as &$Game) {
            $total += $Game->get('size');
            unset($Game);
        }
        return $total;
    }
    
    /**
     * Get games by state
     *
     * @param int $state the state to filter
     *
     * @return array
     */
    public function getByState($state)
    {
        return $this->find(array('state' => $state));
    }
}
