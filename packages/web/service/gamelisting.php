<?php
/**
 * Returns a listing of all games in the system.
 *
 * PHP version 5
 *
 * @category GameListing
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Returns a listing of all games in the system.
 *
 * @category GameListing
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
require '../commons/base.inc.php';
header('Content-Type: text/plain');
try {
    $gameCount = FOGCore::getClass('GameManager')
        ->count();
    if ($gameCount < 1) {
        throw new Exception(
            _('There are no games on this server')
        );
    }
    $gameids = FOGCore::getSubObjectIDs('Game');
    $gamenames = FOGCore::getSubObjectIDs(
        'Game',
        array('id' => $gameids),
        'name'
    );
    $gameversions = FOGCore::getSubObjectIDs(
        'Game',
        array('id' => $gameids),
        'version'
    );
    foreach ((array)$gameids as $index => $gameid) {
        printf(
            '\tID# %d\t-\t%s (v%s)\n',
            $gameid,
            $gamenames[$index],
            $gameversions[$index]
        );
        unset(
            $gameid,
            $gamenames[$index],
            $gameversions[$index],
            $gameids[$index]
        );
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
exit;
