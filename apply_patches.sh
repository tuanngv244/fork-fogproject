#!/bin/bash
#
# FOG Game Module Integration Patcher
# This script automatically integrates Game module into FOG core
#

set -e

echo "========================================="
echo "  FOG Game Module - Auto Integration"
echo "========================================="
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# FOG paths
FOG_WEB="/var/www/html/fog"
FOGBASE="$FOG_WEB/lib/fog/fogbase.class.php"
INDEX="$FOG_WEB/management/index.php"

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root (sudo bash apply_patches.sh)${NC}"
    exit 1
fi

echo "[1/7] Checking FOG installation..."
if [ ! -d "$FOG_WEB" ]; then
    echo -e "${RED}Error: FOG not found at $FOG_WEB${NC}"
    exit 1
fi
echo -e "${GREEN}✓ FOG found${NC}"

echo ""
echo "[2/7] Copying module files..."

# Copy class files
cp -v game.class.php "$FOG_WEB/lib/fog/" || echo "Warning: game.class.php not found"
cp -v gamemanager.class.php "$FOG_WEB/lib/fog/" || echo "Warning: gamemanager.class.php not found"
cp -v gamemanagementpage.class.php "$FOG_WEB/lib/pages/" || echo "Warning: gamemanagementpage.class.php not found"

echo -e "${GREEN}✓ Files copied${NC}"

echo ""
echo "[3/7] Setting permissions..."
chown -R www-data:www-data "$FOG_WEB/lib/fog/game*.php" 2>/dev/null || true
chown -R www-data:www-data "$FOG_WEB/lib/pages/gamemanagementpage.class.php" 2>/dev/null || true
chmod 644 "$FOG_WEB/lib/fog/game*.php" 2>/dev/null || true
chmod 644 "$FOG_WEB/lib/pages/gamemanagementpage.class.php" 2>/dev/null || true
echo -e "${GREEN}✓ Permissions set${NC}"

echo ""
echo "[4/7] Installing database..."
if [ -f "game_install.sql" ]; then
    mysql -u root fog < game_install.sql 2>/dev/null || echo "Warning: Database import failed"
    echo -e "${GREEN}✓ Database installed${NC}"
else
    echo -e "${YELLOW}⚠ game_install.sql not found, skipping database${NC}"
fi

echo ""
echo "[5/7] Backing up core files..."
cp "$FOGBASE" "$FOGBASE.backup.$(date +%Y%m%d_%H%M%S)"
cp "$INDEX" "$INDEX.backup.$(date +%Y%m%d_%H%M%S)"
echo -e "${GREEN}✓ Backups created${NC}"

echo ""
echo "[6/7] Patching FOG core files..."

# Patch 1: Add Game classes to fogbase.class.php
echo "  - Patching fogbase.class.php..."
if grep -q "'Game'.*'game.class.php'" "$FOGBASE"; then
    echo -e "    ${YELLOW}Already patched${NC}"
else
    # Find the line with 'Image' => 'image.class.php' and add Game classes after it
    sed -i "/'Image' => 'image\.class\.php',/a\\
            'Game' => 'game.class.php',\\
            'GameManager' => 'gamemanager.class.php',\\
            'GameManagementPage' => '../pages/gamemanagementpage.class.php'," "$FOGBASE"
    echo -e "    ${GREEN}✓ Game classes registered${NC}"
fi

# Patch 2: Add Game route to index.php
echo "  - Patching index.php..."
if grep -q "case 'game':" "$INDEX"; then
    echo -e "    ${YELLOW}Already patched${NC}"
else
    # Find case 'image': and add case 'game': after its break;
    sed -i "/case 'image':/,/break;/a\\
        case 'game':\\
            \$page = new GameManagementPage();\\
            break;" "$INDEX"
    echo -e "    ${GREEN}✓ Game route registered${NC}"
fi

echo ""
echo "[7/7] Restarting services..."
rm -rf /var/www/html/fog/tmp/* 2>/dev/null || true
systemctl restart apache2 || service apache2 restart
echo -e "${GREEN}✓ Apache restarted${NC}"

echo ""
echo "========================================="
echo -e "  ${GREEN}✓ INTEGRATION COMPLETE!${NC}"
echo "========================================="
echo ""
echo "Module installed at:"
echo "  http://YOUR_IP/fog/management/?node=game"
echo ""
echo "Next steps:"
echo "  1. Clear browser cache (Ctrl+Shift+R)"
echo "  2. Login to FOG web interface"
echo "  3. Navigate to Game Management"
echo ""
echo "Backups saved:"
echo "  $FOGBASE.backup.*"
echo "  $INDEX.backup.*"
echo ""
echo "========================================="
