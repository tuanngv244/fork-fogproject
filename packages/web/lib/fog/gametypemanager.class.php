<?php
/**
 * GameTypeManager class
 *
 * PHP version 5
 *
 * @category GameTypeManager
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * GameTypeManager class
 *
 * @category GameTypeManager
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameTypeManager extends FOGManagerController
{
    /**
     * The base table name
     *
     * @var string
     */
    public $tablename = 'gameTypes';
    
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
                'gtID',
                'gtName',
                'gtIcon',
                'gtDescription'
            ),
            array(
                'INTEGER',
                'VARCHAR(255)',
                'VARCHAR(100)',
                'TEXT'
            ),
            array(
                false,
                false,
                false,
                false
            ),
            false,
            'MyISAM',
            'utf8mb4',
            'gtID',
            'gtID'
        );
        return self::$DB->query($sql);
    }
}
