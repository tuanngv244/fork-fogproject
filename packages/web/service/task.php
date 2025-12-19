<?php
/**
 * Task REST API
 * 
 * Create and manage imaging tasks (Deploy/Capture)
 * Following proper FOG workflow
 *
 * @category TaskAPI
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
 * Format task data
 */
function formatTaskData($Task) {
    if (!$Task || !$Task->isValid()) {
        return null;
    }
    
    $Host = $Task->getHost();
    $Image = $Task->getImage();
    $TaskType = $Task->getTaskType();
    $TaskState = FOGCore::getClass('TaskState', $Task->get('stateID'));
    $StorageNode = FOGCore::getClass('StorageNode', $Task->get('storagenodeID'));
    
    return [
        'id' => (int)$Task->get('id'),
        'name' => $Task->get('name'),
        'hostID' => (int)$Task->get('hostID'),
        'hostName' => $Host->isValid() ? $Host->get('name') : null,
        'imageID' => (int)$Task->get('imageID'),
        'imageName' => $Image->isValid() ? $Image->get('name') : null,
        'taskTypeID' => (int)$Task->get('typeID'),
        'taskType' => $TaskType->isValid() ? $TaskType->get('name') : null,
        'stateID' => (int)$Task->get('stateID'),
        'state' => $TaskState->isValid() ? $TaskState->get('name') : null,
        'storageNodeID' => (int)$Task->get('storagenodeID'),
        'storageNodeName' => $StorageNode->isValid() ? $StorageNode->get('name') : null,
        'percent' => (int)$Task->get('pct'),
        'createdTime' => $Task->get('createdTime'),
        'createdBy' => $Task->get('createdBy'),
        'isDebug' => (bool)$Task->get('isDebug'),
        'shutdown' => (bool)$Task->get('shutdown')
    ];
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($method) {
        case 'GET':
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            
            if ($id) {
                // Get single task
                $Task = FOGCore::getClass('Task', $id);
                
                if (!$Task->isValid()) {
                    sendResponse(false, 'Task not found', null, 404);
                }
                
                sendResponse(true, 'Task retrieved successfully', formatTaskData($Task));
                
            } else {
                // List tasks with filters
                $filters = [];
                
                if (isset($_GET['hostID'])) {
                    $filters['hostID'] = (int)$_GET['hostID'];
                }
                if (isset($_GET['imageID'])) {
                    $filters['imageID'] = (int)$_GET['imageID'];
                }
                if (isset($_GET['stateID'])) {
                    $filters['stateID'] = (int)$_GET['stateID'];
                }
                if (isset($_GET['typeID'])) {
                    $filters['typeID'] = (int)$_GET['typeID'];
                }
                
                // Default: only show queued and in-progress tasks
                if (!isset($_GET['stateID'])) {
                    $filters['stateID'] = FOGCore::fastmerge(
                        FOGCore::getQueuedStates(),
                        [FOGCore::getProgressState()]
                    );
                }
                
                Route::listem('task', 'id', true, $filters);
                $tasks = json_decode(Route::getData());
                
                $data = [];
                foreach ($tasks as $taskObj) {
                    $Task = FOGCore::getClass('Task', $taskObj->id);
                    if ($Task->isValid()) {
                        $data[] = formatTaskData($Task);
                    }
                }
                
                sendResponse(true, 'Tasks retrieved successfully', [
                    'tasks' => $data,
                    'count' => count($data)
                ]);
            }
            break;
            
        case 'POST':
            // Create new task (Deploy or Capture)
            // Following Host::createImagePackage() workflow
            
            // Validate required fields
            if (empty($input['hostID'])) {
                sendResponse(false, 'Host ID is required', null, 400);
            }
            if (empty($input['taskType'])) {
                sendResponse(false, 'Task type is required (1=Deploy, 2=Capture, 8=Multicast)', null, 400);
            }
            
            $hostID = (int)$input['hostID'];
            $taskTypeID = (int)$input['taskType'];
            
            // Load host
            $Host = FOGCore::getClass('Host', $hostID);
            if (!$Host->isValid()) {
                sendResponse(false, 'Host not found', null, 404);
            }
            
            // Check if host is pending
            if ($Host->get('pending')) {
                sendResponse(false, 'Cannot create task for pending host', null, 400);
            }
            
            // Load task type
            $TaskType = FOGCore::getClass('TaskType', $taskTypeID);
            if (!$TaskType->isValid()) {
                sendResponse(false, 'Invalid task type', null, 400);
            }
            
            // Check for existing active task
            $existingTask = $Host->get('task');
            if ($existingTask->isValid()) {
                $existingTaskType = $existingTask->getTaskType();
                if ($existingTaskType->isImagingTask()) {
                    sendResponse(false, 'Host already has an active imaging task', null, 409);
                }
            }
            
            // Load image
            $Image = $Host->getImage();
            if ($TaskType->isImagingTask()) {
                if (!$Image->isValid()) {
                    sendResponse(false, 'Host must have an image assigned for imaging tasks', null, 400);
                }
                if (!$Image->get('isEnabled')) {
                    sendResponse(false, 'Image is not enabled', null, 400);
                }
                if ($TaskType->isCapture() && $Image->get('protected')) {
                    sendResponse(false, 'Cannot capture to protected image', null, 403);
                }
            }
            
            // Get storage group and node
            $StorageGroup = $Image->getStorageGroup();
            if (!$StorageGroup->isValid()) {
                sendResponse(false, 'Image storage group is not valid', null, 400);
            }
            
            // Select appropriate storage node
            if ($TaskType->isCapture()) {
                $StorageNode = $StorageGroup->getMasterStorageNode();
            } else {
                $StorageNode = $Host->getOptimalStorageNode();
            }
            
            if (!$StorageNode->isValid()) {
                sendResponse(false, 'No valid storage node found', null, 500);
            }
            
            // Task options
            $taskName = isset($input['name']) ? $input['name'] : $TaskType->get('name') . ' Task - ' . $Host->get('name');
            $shutdown = isset($input['shutdown']) ? (bool)$input['shutdown'] : false;
            $debug = isset($input['debug']) ? (bool)$input['debug'] : false;
            $wol = isset($input['wol']) ? (bool)$input['wol'] : false;
            $username = isset($input['username']) ? $input['username'] : 'API';
            
            // Use Host::createImagePackage method
            try {
                $result = $Host->createImagePackage(
                    $taskTypeID,
                    $taskName,
                    $shutdown,
                    $debug,
                    false, // snapins
                    false, // is group task
                    $username,
                    '', // passreset
                    false, // session join
                    $wol
                );
                
                // Reload task
                $Task = $Host->get('task');
                
                sendResponse(true, 'Task created successfully', formatTaskData($Task), 201);
                
            } catch (Exception $e) {
                sendResponse(false, 'Failed to create task: ' . $e->getMessage(), null, 500);
            }
            break;
            
        case 'DELETE':
            // Cancel task
            $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
            
            if (!$id) {
                sendResponse(false, 'Task ID is required', null, 400);
            }
            
            $Task = FOGCore::getClass('Task', $id);
            
            if (!$Task->isValid()) {
                sendResponse(false, 'Task not found', null, 404);
            }
            
            $taskName = $Task->get('name');
            
            if (!$Task->cancel()) {
                sendResponse(false, 'Failed to cancel task', null, 500);
            }
            
            sendResponse(true, "Task '$taskName' cancelled successfully");
            break;
            
        default:
            sendResponse(false, 'Method not allowed', null, 405);
    }
    
} catch (Exception $e) {
    sendResponse(false, 'Error: ' . $e->getMessage(), null, 500);
}
