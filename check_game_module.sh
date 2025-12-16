#!/bin/bash
#
# Quick Check and Fix for Game Module
# Run this on FOG server
#

echo "======================================"
echo "  FOG Game Module - Quick Check"
echo "======================================"
echo ""

# Check if files exist
echo "[1] Checking module files..."
if [ -f "/var/www/html/fog/lib/fog/game.class.php" ]; then
    echo "  ✓ game.class.php found"
else
    echo "  ✗ game.class.php NOT found"
    echo "  Copy it to: /var/www/html/fog/lib/fog/"
fi

if [ -f "/var/www/html/fog/lib/fog/gamemanager.class.php" ]; then
    echo "  ✓ gamemanager.class.php found"
else
    echo "  ✗ gamemanager.class.php NOT found"
    echo "  Copy it to: /var/www/html/fog/lib/fog/"
fi

if [ -f "/var/www/html/fog/lib/pages/gamemanagementpage.class.php" ]; then
    echo "  ✓ gamemanagementpage.class.php found"
else
    echo "  ✗ gamemanagementpage.class.php NOT found"
    echo "  Copy it to: /var/www/html/fog/lib/pages/"
fi

echo ""
echo "[2] Checking database..."
mysql -u root fog -e "SHOW TABLES LIKE 'games';" 2>/dev/null | grep -q "games"
if [ $? -eq 0 ]; then
    echo "  ✓ Table 'games' exists"
    GAME_COUNT=$(mysql -u root fog -e "SELECT COUNT(*) FROM games;" -sN 2>/dev/null)
    echo "  ✓ Games in database: $GAME_COUNT"
else
    echo "  ✗ Table 'games' NOT found"
    echo "  Run: mysql -u root fog < game_install.sql"
fi

echo ""
echo "[3] Checking class registration (CRITICAL!)..."
grep -q "'Game'.*'game.class.php'" /var/www/html/fog/lib/fog/fogbase.class.php
if [ $? -eq 0 ]; then
    echo "  ✓ Game classes registered in fogbase.class.php"
else
    echo "  ✗ Game classes NOT registered - THIS IS THE PROBLEM!"
    echo "  NEED TO PATCH: /var/www/html/fog/lib/fog/fogbase.class.php"
fi

echo ""
echo "[4] Checking route registration (CRITICAL!)..."
grep -q "case 'game':" /var/www/html/fog/management/index.php
if [ $? -eq 0 ]; then
    echo "  ✓ Game route registered in index.php"
else
    echo "  ✗ Game route NOT registered - THIS IS THE PROBLEM!"
    echo "  NEED TO PATCH: /var/www/html/fog/management/index.php"
fi

echo ""
echo "======================================"
echo "  DIAGNOSIS"
echo "======================================"

NEEDS_PATCH=0
if ! grep -q "'Game'.*'game.class.php'" /var/www/html/fog/lib/fog/fogbase.class.php; then
    NEEDS_PATCH=1
fi
if ! grep -q "case 'game':" /var/www/html/fog/management/index.php; then
    NEEDS_PATCH=1
fi

if [ $NEEDS_PATCH -eq 1 ]; then
    echo ""
    echo "⚠️  MODULE NOT INTEGRATED!"
    echo ""
    echo "Files exist but FOG doesn't know about them."
    echo "You MUST apply patches to integrate the module."
    echo ""
    echo "======================================"
    echo "  FIX NOW"
    echo "======================================"
    echo ""
    echo "Run these commands:"
    echo ""
    echo "# Option 1: Auto patch (recommended)"
    echo "bash apply_patches.sh"
    echo ""
    echo "# Option 2: Manual patch"
    echo "nano /var/www/html/fog/lib/fog/fogbase.class.php"
    echo "  # Add after 'Image' => 'image.class.php':"
    echo "  'Game' => 'game.class.php',"
    echo "  'GameManager' => 'gamemanager.class.php',"
    echo "  'GameManagementPage' => '../pages/gamemanagementpage.class.php',"
    echo ""
    echo "nano /var/www/html/fog/management/index.php"
    echo "  # Add after case 'image':"
    echo "  case 'game':"
    echo "      \$page = new GameManagementPage();"
    echo "      break;"
    echo ""
    echo "# Then restart"
    echo "systemctl restart apache2"
    echo "rm -rf /var/www/html/fog/tmp/*"
    echo ""
else
    echo ""
    echo "✓ Module is properly integrated!"
    echo ""
    echo "If you still don't see it:"
    echo "1. Clear browser cache (Ctrl+Shift+R)"
    echo "2. Check Apache error log: tail -f /var/log/apache2/error.log"
    echo "3. Access directly: http://YOUR_IP/fog/management/?node=game"
    echo ""
fi
