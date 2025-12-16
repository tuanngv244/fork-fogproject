#!/bin/bash
#
# Fix Game Module File Locations
#

echo "========================================="
echo "  Fix Game Module Files"
echo "========================================="
echo ""

# Check current locations
echo "[1] Checking current file locations:"
find /var/www/html/fog -name "gamemanagementpage.class.php" -o -name "game.class.php" -o -name "gamemanager.class.php"

echo ""
echo "[2] Moving files to correct locations..."

# Ensure directories exist
mkdir -p /var/www/html/fog/lib/fog
mkdir -p /var/www/html/fog/lib/pages

# Move files to correct locations
if [ -f "/var/www/html/fog/lib/fog/gamemanagementpage.class.php" ]; then
    mv /var/www/html/fog/lib/fog/gamemanagementpage.class.php /var/www/html/fog/lib/pages/
    echo "✓ Moved gamemanagementpage.class.php to lib/pages/"
fi

if [ ! -f "/var/www/html/fog/lib/fog/game.class.php" ]; then
    echo "✗ game.class.php not found in lib/fog/"
fi

if [ ! -f "/var/www/html/fog/lib/fog/gamemanager.class.php" ]; then
    echo "✗ gamemanager.class.php not found in lib/fog/"
fi

echo ""
echo "[3] Setting correct permissions..."
chown -R www-data:www-data /var/www/html/fog/lib/fog/game*.php 2>/dev/null
chown -R www-data:www-data /var/www/html/fog/lib/pages/gamemanagementpage.class.php 2>/dev/null
chmod 644 /var/www/html/fog/lib/fog/game*.php 2>/dev/null
chmod 644 /var/www/html/fog/lib/pages/gamemanagementpage.class.php 2>/dev/null
echo "✓ Permissions set"

echo ""
echo "[4] Final verification:"
echo "Files should be at:"
echo "  /var/www/html/fog/lib/fog/game.class.php"
ls -la /var/www/html/fog/lib/fog/game.class.php 2>&1
echo "  /var/www/html/fog/lib/fog/gamemanager.class.php"
ls -la /var/www/html/fog/lib/fog/gamemanager.class.php 2>&1
echo "  /var/www/html/fog/lib/pages/gamemanagementpage.class.php"
ls -la /var/www/html/fog/lib/pages/gamemanagementpage.class.php 2>&1

echo ""
echo "[5] Clearing cache and restarting..."
rm -rf /var/www/html/fog/tmp/*
systemctl restart apache2
echo "✓ Done"

echo ""
echo "========================================="
echo "Now try accessing:"
echo "  http://$(hostname -I | awk '{print $1}')/fog/management/?node=game"
echo "========================================="
