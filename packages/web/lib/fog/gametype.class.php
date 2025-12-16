<?php
/**
 * GameType class
 *
 * PHP version 5
 *
 * @category GameType
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * GameType class
 *
 * @category GameType
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
class GameType extends FOGController
{
    /**
     * The game type table
     *
     * @var string
     */
    protected $databaseTable = 'gameTypes';
    
    /**
     * The game type fields and common names
     *
     * @var array
     */
    protected $databaseFields = array(
        'id' => 'gtID',
        'name' => 'gtName',
        'icon' => 'gtIcon',
        'description' => 'gtDescription',
    );
    
    /**
     * The required fields
     *
     * @var array
     */
    protected $databaseFieldsRequired = array(
        'name',
    );
}
