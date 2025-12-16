<?php
/**
 * Game management page - Minimal Version
 *
 * PHP version 5
 *
 * @category GameManagementPage
 * @package  FOGProject
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 */
class GameManagementPage extends FOGPage
{
    public $node = 'game';
    
    public function __construct($name = '')
    {
        $this->name = 'Game Management';
        parent::__construct($this->name);
    }
    
    public function index()
    {
        echo '<h2>Game Management Module</h2>';
        echo '<p>Module loaded successfully!</p>';
        
        // Test database
        try {
            $count = self::getClass('GameManager')->count();
            echo '<p>Total games in database: ' . $count . '</p>';
            
            // List games
            $games = self::getClass('GameManager')->find();
            if (count($games) > 0) {
                echo '<table class="table table-striped">';
                echo '<thead><tr><th>ID</th><th>Name</th><th>State</th></tr></thead>';
                echo '<tbody>';
                foreach ($games as $game) {
                    echo '<tr>';
                    echo '<td>' . $game->get('id') . '</td>';
                    echo '<td>' . $game->get('name') . '</td>';
                    echo '<td>' . $game->get('state') . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p>No games found.</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
        }
    }
}
