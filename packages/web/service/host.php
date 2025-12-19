<?php
/**
 * Host REST API
 * 
 * Manage FOG Hosts
 *
 * @category HostAPI
 * @package  FOGProject
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 */

require '../commons/base.inc.php';

header('Content-Type: application/json');

/**
 * Send JSON response
 */
function sendResponse($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_PRETTY_PRINT);
    exit;
}

/**
 * Format host data
 */
function formatHostData($Host) {
    if (!$Host || !$Host->isValid()) {
        return null;
    }
    
    $Image = $Host->getImage();
    $Task = $Host->get('task');
    
    $data = [
        'id' => (int)$Host->get('id'),
        'name' => $Host->get('name'),
        'description' => $Host->get('description'),
        'mac' => $Host->get('mac')->__toString(),
        'imageID' => (int)$Host->get('imageID'),
        'imageName' => $Image->isValid() ? $Image->get('name') : null,
        'pending' => $Host->get('pending') ? true : false,
        'deployed' => $Host->get('deployed'),
        'pub_key' => $Host->get('pub_key')
    ];
    
    // Add current task if exists
    if ($Task->isValid()) {
        $TaskType = $Task->getTaskType();
        $TaskState = FOGCore::getClass('TaskState', $Task->get('stateID'));
        
        $data['currentTask'] = [
            'id' => (int)$Task->get('id'),
            'name' => $Task->get('name'),
            'typeID' => (int)$Task->get('typeID'),
            'type' => $TaskType->isValid() ? $TaskType->get('name') : null,
            'stateID' => (int)$Task->get('stateID'),
            'state' => $TaskState->isValid() ? $TaskState->get('name') : null,
            'percent' => (int)$Task->get('pct')
        ];
    }
    
    return $data;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($method) {
        case 'GET':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            $mac = isset($_GET['mac']) ? trim($_GET['mac']) : null;
            
            if ($id || $mac) {
                // Get single host
                if ($mac) {
                    $hostIDs = FOGCore::getSubObjectIDs(
                        'MACAddressAssociation',
                        ['mac' => $mac],
                        'hostID'
                    );
                    $id = !empty($hostIDs) ? $hostIDs[0] : 0;
                }
                
                $Host = FOGCore::getClass('Host', $id);
                
                if (!$Host->isValid()) {
                    sendResponse(false, 'Host not found', null, 404);
                }
                
                sendResponse(true, 'Host retrieved successfully', formatHostData($Host));
                
            } else {
                // List hosts
                $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                $limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 50;
                $offset = ($page - 1) * $limit;
                
                $filters = [];
                if (isset($_GET['imageID'])) {
                    $filters['imageID'] = (int)$_GET['imageID'];
                }
                if (isset($_GET['pending'])) {
                    $filters['pending'] = $_GET['pending'] == '1' ? '1' : '';
                }
                
                Route::listem('host', 'name', false, $filters);
                $hosts = json_decode(Route::getData());
                
                $total = count($hosts);
                $hosts = array_slice($hosts, $offset, $limit);
                
                $data = [];
                foreach ($hosts as $hostObj) {
                    $Host = FOGCore::getClass('Host', $hostObj->id);
                    if ($Host->isValid()) {
                        $data[] = formatHostData($Host);
                    }
                }
                
                sendResponse(true, 'Hosts retrieved successfully', [
                    'hosts' => $data,
                    'pagination' => [
                        'total' => $total,
                        'page' => $page,
                        'limit' => $limit,
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            }
            break;
            
        case 'POST':
            // Create new host
            if (empty($input['name'])) {
                sendResponse(false, 'Host name is required', null, 400);
            }
            if (empty($input['mac'])) {
                sendResponse(false, 'MAC address is required', null, 400);
            }
            
            // Check if host name exists
            if (FOGCore::getClass('HostManager')->exists($input['name'])) {
                sendResponse(false, 'Host name already exists', null, 400);
            }
            
            // Validate MAC
            $MAC = FOGCore::getClass('MACAddress', $input['mac']);
            if (!$MAC->isValid()) {
                sendResponse(false, 'Invalid MAC address', null, 400);
            }
            
            // Check if MAC exists
            $macExists = FOGCore::getSubObjectIDs(
                'MACAddressAssociation',
                ['mac' => $MAC->__toString()]
            );
            if (!empty($macExists)) {
                sendResponse(false, 'MAC address already registered', null, 409);
            }
            
            // Create host
            $Host = FOGCore::getClass('Host')
                ->set('name', $input['name'])
                ->set('description', isset($input['description']) ? $input['description'] : '')
                ->set('imageID', isset($input['imageID']) ? (int)$input['imageID'] : 0)
                ->set('pending', isset($input['pending']) ? $input['pending'] : '');
            
            if (!$Host->save()) {
                sendResponse(false, 'Failed to create host', null, 500);
            }
            
            // Add MAC address
            $Host->addPriMAC($input['mac']);
            
            sendResponse(true, 'Host created successfully', formatHostData($Host), 201);
            break;
            
        case 'PUT':
            // Update host
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            
            if (!$id) {
                sendResponse(false, 'Host ID is required', null, 400);
            }
            
            $Host = FOGCore::getClass('Host', $id);
            
            if (!$Host->isValid()) {
                sendResponse(false, 'Host not found', null, 404);
            }
            
            // Update fields
            $updateFields = ['name', 'description', 'imageID', 'pending'];
            
            foreach ($updateFields as $field) {
                if (isset($input[$field])) {
                    $Host->set($field, $input[$field]);
                }
            }
            
            if (!$Host->save()) {
                sendResponse(false, 'Failed to update host', null, 500);
            }
            
            sendResponse(true, 'Host updated successfully', formatHostData($Host));
            break;
            
        case 'DELETE':
            // Delete host
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            
            if (!$id) {
                sendResponse(false, 'Host ID is required', null, 400);
            }
            
            $Host = FOGCore::getClass('Host', $id);
            
            if (!$Host->isValid()) {
                sendResponse(false, 'Host not found', null, 404);
            }
            
            $hostName = $Host->get('name');
            
            if (!$Host->destroy()) {
                sendResponse(false, 'Failed to delete host', null, 500);
            }
            
            sendResponse(true, "Host '$hostName' deleted successfully");
            break;
            
        default:
            sendResponse(false, 'Method not allowed', null, 405);
    }
    
} catch (Exception $e) {
    sendResponse(false, 'Error: ' . $e->getMessage(), null, 500);
}
