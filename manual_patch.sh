#!/bin/bash
#
# Manual Integration for Game Module
# Use this if automatic patches failed
#

echo "========================================="
echo "  Game Module - Manual Integration"
echo "========================================="
echo ""

FOGBASE="/var/www/html/fog/lib/fog/fogbase.class.php"
INDEX="/var/www/html/fog/management/index.php"

echo "[Step 1] Checking current state..."
echo ""

echo "Checking fogbase.class.php for Game classes:"
if grep -q "'Game'.*'game.class.php'" "$FOGBASE"; then
    echo "  ✓ Already registered"
else
    echo "  ✗ NOT registered - Need to add manually!"
    echo ""
    echo "Opening fogbase.class.php for editing..."
    echo "File: $FOGBASE"
    echo ""
    echo "Find this section (around line 100-150):"
    grep -n "'Image' => 'image.class.php'" "$FOGBASE"
    echo ""
    echo "Add these 3 lines AFTER the 'Image' line:"
    echo ""
    echo "            'Game' => 'game.class.php',"
    echo "            'GameManager' => 'gamemanager.class.php',"
    echo "            'GameManagementPage' => '../pages/gamemanagementpage.class.php',"
    echo ""
    read -p "Press Enter to edit fogbase.class.php with nano..."
    nano +$(grep -n "'Image' => 'image.class.php'" "$FOGBASE" | cut -d: -f1) "$FOGBASE"
fi

echo ""
echo "[Step 2] Checking index.php for game route:"
if grep -q "case 'game':" "$INDEX"; then
    echo "  ✓ Already registered"
else
    echo "  ✗ NOT registered - Need to add manually!"
    echo ""
    echo "Opening index.php for editing..."
    echo "File: $INDEX"
    echo ""
    echo "Find this section (around line 200-300):"
    grep -n "case 'image':" "$INDEX" | head -3
    echo ""
    echo "Add these 3 lines AFTER the 'image' case block:"
    echo ""
    echo "        case 'game':"
    echo "            \$page = new GameManagementPage();"
    echo "            break;"
    echo ""
    read -p "Press Enter to edit index.php with nano..."
    nano +$(grep -n "case 'image':" "$INDEX" | head -1 | cut -d: -f1) "$INDEX"
fi

echo ""
echo "[Step 3] Restarting services..."
rm -rf /var/www/html/fog/tmp/*
systemctl restart apache2 || service apache2 restart
echo "  ✓ Apache restarted"

echo ""
echo "[Step 4] Verification..."
echo ""
echo "Check if patches applied:"
echo ""
echo "1. Game classes in fogbase.class.php:"
grep -A 2 "'Game' => 'game.class.php'" "$FOGBASE" || echo "   FAILED - Still not found!"
echo ""
echo "2. Game route in index.php:"
grep -A 2 "case 'game':" "$INDEX" || echo "   FAILED - Still not found!"

echo ""
echo "========================================="
echo "  After editing, test access:"
echo "  http://$(hostname -I | awk '{print $1}')/fog/management/?node=game"
echo "========================================="
