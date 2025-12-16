<?php
/**
 * The game object
 *
 * PHP version 5
 *
 * @category Game
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * The game object
 *
 * @category Game
 * @package  FOGProject
 * @author   FOG Project
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
        'description' => 'gameDescription',
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
        'runCount' => 'gameRunCount',
        'version' => 'gameVersion',
        'publisher' => 'gamePublisher',
        'genre' => 'gameGenre',
        'rating' => 'gameRating',
        'releaseDate' => 'gameReleaseDate',
        'requirements' => 'gameRequirements',
        'notes' => 'gameNotes',
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
    );
    /**
     * Database -> Class field relationships
     *
     * @var array
     */
    protected $databaseFieldClassRelationships = array(
    );
}
