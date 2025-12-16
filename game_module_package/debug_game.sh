#!/bin/bash
#
# Debug Game Module Loading
#

echo "========================================="
echo "  Game Module Debug"
echo "========================================="
echo ""

echo "[1] Check files exist:"
ls -la /var/www/html/fog/lib/pages/gamemanagementpage.class.php
ls -la /var/www/html/fog/lib/fog/game.class.php
ls -la /var/www/html/fog/lib/fog/gamemanager.class.php

echo ""
echo "[2] Check file syntax:"
php -l /var/www/html/fog/lib/pages/gamemanagementpage.class.php
php -l /var/www/html/fog/lib/fog/game.class.php
php -l /var/www/html/fog/lib/fog/gamemanager.class.php

echo ""
echo "[3] Check class name in file:"
grep "^class GameManagementPage" /var/www/html/fog/lib/pages/gamemanagementpage.class.php

echo ""
echo "[4] Check node property:"
grep "public \$node" /var/www/html/fog/lib/pages/gamemanagementpage.class.php

echo ""
echo "[5] Check database:"
mysql -u root fog -e "SELECT COUNT(*) as game_count FROM games;" 2>&1

echo ""
echo "[6] Test PHP class loading:"
php -r "
require '/var/www/html/fog/commons/base.inc.php';
\$class = FOGCore::getClass('GameManagementPage');
echo 'Class loaded: ' . get_class(\$class) . PHP_EOL;
echo 'Node: ' . \$class->node . PHP_EOL;
" 2>&1

echo ""
echo "[7] Check Apache error log (last 20 lines):"
tail -n 20 /var/log/apache2/error.log | grep -i "game\|fatal\|error"

echo ""
echo "========================================="
echo "  Test URL Access"
echo "========================================="
echo ""
echo "Try accessing:"
echo "  http://$(hostname -I | awk '{print $1}')/fog/management/?node=game"
echo ""
echo "If blank page, run:"
echo "  tail -f /var/log/apache2/error.log"
echo "  (then refresh the page to see errors)"
echo ""
