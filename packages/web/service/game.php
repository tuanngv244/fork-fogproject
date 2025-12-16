<?php
/**
 * Game API service
 *
 * Provides REST-like API for Game Management operations.
 * Supports external system integration without authentication.
 *
 * PHP version 5
 *
 * @category GameAPI
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
/**
 * Game API service
 *
 * @category GameAPI
 * @package  FOGProject
 * @author   FOG Project
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://fogproject.org
 */
require '../commons/base.inc.php';

/**
 * API Response helper
 *
 * @param mixed  $data    Data to return
 * @param int    $code    HTTP status code
 * @param string $message Optional message
 *
 * @return void
 */
function apiResponse($data, $code = 200, $message = null)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(
        array(
            'success' => $code < 400,
            'message' => $message,
            'data' => $data
        )
    );
    exit;
}

/**
 * Get request method (version-safe)
 *
 * @return string
 */
function getRequestMethod()
{
    $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    if (!$method && isset($_SERVER['REQUEST_METHOD'])) {
        $method = $_SERVER['REQUEST_METHOD'];
    }
    return strtoupper($method);
}

/**
 * Get JSON input
 *
 * @return array
 */
function getJsonInput()
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?: array();
}

/**
 * Get request parameter (GET, POST, or JSON)
 *
 * @param string $key     Parameter name
 * @param mixed  $default Default value
 *
 * @return mixed
 */
function getParam($key, $default = null)
{
    $value = filter_input(INPUT_GET, $key);
    if ($value === null) {
        $value = filter_input(INPUT_POST, $key);
    }
    if ($value === null) {
        $json = getJsonInput();
        $value = isset($json[$key]) ? $json[$key] : null;
    }
    return $value !== null ? $value : $default;
}

try {
    $method = getRequestMethod();
    $action = getParam('action', 'list');
    $id = getParam('id');

    // Route based on method and action
    switch ($method) {
    case 'GET':
        if ($id) {
            // Get single game
            $Game = FOGCore::getClass('Game', $id);
            if (!$Game->isValid()) {
                apiResponse(null, 404, 'Game not found');
            }
            
            apiResponse(
                array(
                    'id' => $Game->get('id'),
                    'name' => $Game->get('name'),
                    'path' => $Game->get('path'),
                    'version' => $Game->get('version'),
                    'gameTypeID' => $Game->get('gameTypeID'),
                    'syncMethod' => $Game->get('syncMethod'),
                    'size' => $Game->get('size'),
                    'enabled' => (bool)$Game->get('enabled'),
                    'autoSync' => (bool)$Game->get('autoSync'),
                    'notes' => $Game->get('notes')
                )
            );
        } elseif ($action === 'hosts') {
            // Get games for a host
            $hostID = getParam('hostID');
            if (!$hostID) {
                apiResponse(null, 400, 'hostID required');
            }
            
            $Host = FOGCore::getClass('Host', $hostID);
            if (!$Host->isValid()) {
                apiResponse(null, 404, 'Host not found');
            }
            
            $games = array();
            foreach ((array)$Host->get('games') as $Game) {
                if (!$Game->isValid()) {
                    continue;
                }
                $games[] = array(
                    'id' => $Game->get('id'),
                    'name' => $Game->get('name'),
                    'version' => $Game->get('version'),
                    'size' => $Game->get('size')
                );
            }
            
            apiResponse($games);
        } elseif ($action === 'groups') {
            // Get games for a group
            $groupID = getParam('groupID');
            if (!$groupID) {
                apiResponse(null, 400, 'groupID required');
            }
            
            $Group = FOGCore::getClass('Group', $groupID);
            if (!$Group->isValid()) {
                apiResponse(null, 404, 'Group not found');
            }
            
            $games = array();
            foreach ((array)$Group->get('games') as $Game) {
                if (!$Game->isValid()) {
                    continue;
                }
                $games[] = array(
                    'id' => $Game->get('id'),
                    'name' => $Game->get('name'),
                    'version' => $Game->get('version'),
                    'size' => $Game->get('size')
                );
            }
            
            apiResponse($games);
        } else {
            // List all games
            $games = array();
            $GameManager = FOGCore::getClass('GameManager');
            
            // Support pagination
            $limit = (int)getParam('limit', 100);
            $offset = (int)getParam('offset', 0);
            
            foreach ($GameManager->find() as $Game) {
                if (!$Game->isValid()) {
                    continue;
                }
                $games[] = array(
                    'id' => $Game->get('id'),
                    'name' => $Game->get('name'),
                    'path' => $Game->get('path'),
                    'version' => $Game->get('version'),
                    'size' => $Game->get('size'),
                    'enabled' => (bool)$Game->get('enabled')
                );
            }
            
            // Apply pagination
            $total = count($games);
            $games = array_slice($games, $offset, $limit);
            
            apiResponse(
                array(
                    'total' => $total,
                    'count' => count($games),
                    'offset' => $offset,
                    'limit' => $limit,
                    'games' => $games
                )
            );
        }
        break;
    case 'POST':
        // Create new game
        $name = getParam('name');
        $path = getParam('path');
        
        if (!$name || !$path) {
            apiResponse(null, 400, 'name and path required');
        }
        
        // Check for duplicate
        $existing = FOGCore::getClass('GameManager')
            ->find(array('name' => $name));
        if (count($existing) > 0) {
            apiResponse(null, 409, 'Game with this name already exists');
        }
        
        $Game = FOGCore::getClass('Game')
            ->set('name', $name)
            ->set('path', $path)
            ->set('version', getParam('version', '1.0'))
            ->set('gameTypeID', getParam('gameTypeID', 1))
            ->set('syncMethod', getParam('syncMethod', 'rsync'))
            ->set('enabled', getParam('enabled', 1))
            ->set('autoSync', getParam('autoSync', 0))
            ->set('notes', getParam('notes', ''));
        
        if (!$Game->save()) {
            apiResponse(null, 500, 'Failed to create game');
        }
        
        FOGCore::getClass('EventManager')
            ->notify(
                'GAME_ADD',
                array('Game' => &$Game)
            );
        
        apiResponse(
            array(
                'id' => $Game->get('id'),
                'name' => $Game->get('name'),
                'path' => $Game->get('path')
            ),
            201,
            'Game created successfully'
        );
        break;

    case 'PUT':
        // Update game
        if (!$id) {
            apiResponse(null, 400, 'id required');
        }
        
        $Game = FOGCore::getClass('Game', $id);
        if (!$Game->isValid()) {
            apiResponse(null, 404, 'Game not found');
        }
        
        $name = getParam('name');
        if ($name) {
            $Game->set('name', $name);
        }
        
        $path = getParam('path');
        if ($path) {
            $Game->set('path', $path);
        }
        
        $version = getParam('version');
        if ($version) {
            $Game->set('version', $version);
        }
        
        $enabled = getParam('enabled');
        if ($enabled !== null) {
            $Game->set('enabled', (int)$enabled);
        }
        
        $autoSync = getParam('autoSync');
        if ($autoSync !== null) {
            $Game->set('autoSync', (int)$autoSync);
        }
        
        $notes = getParam('notes');
        if ($notes !== null) {
            $Game->set('notes', $notes);
        }
        
        if (!$Game->save()) {
            apiResponse(null, 500, 'Failed to update game');
        }
        
        FOGCore::getClass('EventManager')
            ->notify(
                'GAME_UPDATE',
                array('Game' => &$Game)
            );
        
        apiResponse(
            array('id' => $Game->get('id')),
            200,
            'Game updated successfully'
        );
        break;

    case 'DELETE':
        // Delete game
        if (!$id) {
            apiResponse(null, 400, 'id required');
        }
        
        $Game = FOGCore::getClass('Game', $id);
        if (!$Game->isValid()) {
            apiResponse(null, 404, 'Game not found');
        }
        
        FOGCore::getClass('EventManager')
            ->notify(
                'GAME_DELETE',
                array('Game' => &$Game)
            );
        
        if (!$Game->destroy()) {
            apiResponse(null, 500, 'Failed to delete game');
        }
        
        apiResponse(
            array('id' => $id),
            200,
            'Game deleted successfully'
        );
        break;

    default:
        apiResponse(null, 405, 'Method not allowed');
    }
} catch (Exception $e) {
    apiResponse(
        null,
        500,
        $e->getMessage()
    );
}
