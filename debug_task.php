<?php
/**
 * Debug script to test FOG Task creation
 * Run with: sudo php debug_task.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Path relative to fogproject directory
$fogBasePath = __DIR__ . '/packages/web/commons/base.inc.php';

if (!file_exists($fogBasePath)) {
    die("ERROR: Cannot find FOG base.inc.php at: {$fogBasePath}\n");
}

require $fogBasePath;

echo "=== FOG Task Creation Debug Script ===\n\n";

try {
    echo "1. Testing database connection...\n";
    $db = FOGCore::getClass('DatabaseManager');
    echo "   ✓ Database connected\n\n";
    
    echo "2. Getting host PC-3...\n";
    $Host = FOGCore::getClass('Host')->set('name', 'PC-3')->load('name');
    
    if (!$Host->isValid()) {
        throw new Exception("Host PC-3 not found!");
    }
    
    echo "   ✓ Host ID: " . $Host->get('id') . "\n";
    echo "   ✓ Host Name: " . $Host->get('name') . "\n";
    echo "   ✓ Host MAC: " . $Host->get('mac') . "\n";
    echo "   ✓ Host Image ID: " . $Host->get('imageID') . "\n\n";
    
    echo "3. Getting image...\n";
    $imageID = $Host->get('imageID');
    if (!$imageID) {
        throw new Exception("Host has no image assigned!");
    }
    
    $Image = FOGCore::getClass('Image')->set('id', $imageID)->load();
    if (!$Image->isValid()) {
        throw new Exception("Image ID {$imageID} not found!");
    }
    
    echo "   ✓ Image ID: " . $Image->get('id') . "\n";
    echo "   ✓ Image Name: " . $Image->get('name') . "\n\n";
    
    echo "4. Checking existing tasks for this host...\n";
    $existingTasks = FOGCore::getClass('TaskManager')
        ->find(['hostID' => $Host->get('id')]);
    echo "   Found " . count($existingTasks) . " existing task(s)\n\n";
    
    echo "5. Creating task using FOG's createImagePackage method...\n";
    $taskType = 1; // 1 = Deploy
    $taskName = 'Debug Deploy Task';
    $deploySnapins = false;
    $deployDebug = false;
    $wol = true;
    $scheduleDeployTime = '';
    
    $Task = $Host->createImagePackage(
        $taskType,
        $taskName,
        $deploySnapins,
        $deployDebug,
        $wol,
        $scheduleDeployTime
    );
    
    if ($Task && $Task->isValid()) {
        echo "   ✓ Task created successfully!\n";
        echo "   ✓ Task ID: " . $Task->get('id') . "\n";
        echo "   ✓ Task Name: " . $Task->get('name') . "\n";
        echo "   ✓ Task Type ID: " . $Task->get('typeID') . "\n";
        echo "   ✓ Task State ID: " . $Task->get('stateID') . "\n\n";
    } else {
        throw new Exception("Task object created but is not valid!");
    }
    
    echo "6. Verifying task in database...\n";
    $verifyTask = FOGCore::getClass('Task')->set('id', $Task->get('id'))->load();
    
    if ($verifyTask->isValid()) {
        echo "   ✓ Task verified in database!\n";
        echo "   ✓ Task exists with ID: " . $verifyTask->get('id') . "\n\n";
    } else {
        echo "   ✗ Task NOT found in database!\n\n";
    }
    
    echo "7. Querying tasks table directly...\n";
    $result = $db->query(
        "SELECT taskID, taskName, taskHostID, taskStateID 
         FROM tasks 
         WHERE taskHostID = " . $Host->get('id') . " 
         ORDER BY taskID DESC 
         LIMIT 5"
    );
    
    if ($result && count($result) > 0) {
        echo "   Found " . count($result) . " task(s) in database:\n";
        foreach ($result as $row) {
            echo "   - Task ID: {$row['taskID']}, Name: {$row['taskName']}, State: {$row['taskStateID']}\n";
        }
    } else {
        echo "   ✗ No tasks found in database for this host!\n";
    }
    
    echo "\n=== DEBUG COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
