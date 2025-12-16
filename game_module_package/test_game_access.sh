#!/bin/bash
#
# Direct test accessing game node
#

echo "Testing direct access to game module..."
echo ""

# Test 1: Check if FOGPageManager can find the class
echo "[Test 1] Check if page class is discoverable:"
php -r "
define('BASEPATH', '/var/www/html/fog/');
define('DS', DIRECTORY_SEPARATOR);
\$node = 'game';
\$file = '/var/www/html/fog/lib/pages/gamemanagementpage.class.php';
if (file_exists(\$file)) {
    require_once \$file;
    \$class = new GameManagementPage();
    echo 'Class instantiated: ' . get_class(\$class) . PHP_EOL;
    echo 'Node property: ' . \$class->node . PHP_EOL;
} else {
    echo 'File not found!' . PHP_EOL;
}
"

echo ""
echo "[Test 2] Simulate FOG page loading:"
curl -s "http://localhost/fog/management/?node=game" > /tmp/game_output.html
SIZE=$(wc -c < /tmp/game_output.html)
echo "Response size: $SIZE bytes"

if [ $SIZE -lt 100 ]; then
    echo "ERROR: Response too small (likely blank page or redirect)"
    echo "Content:"
    cat /tmp/game_output.html
else
    echo "SUCCESS: Page loaded with content"
    grep -o "<title>.*</title>" /tmp/game_output.html || echo "No title found"
fi

echo ""
echo "[Test 3] Check what node parameter does:"
php << 'PHPCODE'
<?php
$_GET['node'] = 'game';
require '/var/www/html/fog/commons/base.inc.php';

echo "Requested node: " . ($node ?? 'not set') . PHP_EOL;
echo "FOGPageManager exists: " . (class_exists('FOGPageManager') ? 'yes' : 'no') . PHP_EOL;

try {
    $FOGPageManager = FOGCore::getClass('FOGPageManager');
    echo "FOGPageManager created successfully" . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
?>
PHPCODE

echo ""
echo "========================================="
echo "If tests pass but browser shows blank:"
echo "1. Clear browser cache completely"
echo "2. Try incognito/private mode"
echo "3. Check browser console for JS errors"
echo "========================================="
