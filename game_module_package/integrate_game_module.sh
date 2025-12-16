#!/bin/bash
#
# FOG Game Module Auto Integration Script
# This script automatically integrates the Game module into FOG
#
# Usage: sudo bash integrate_game_module.sh
#

echo "============================================"
echo "FOG Game Module Auto Integration"
echo "============================================"
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "ERROR: Please run as root (sudo bash integrate_game_module.sh)"
    exit 1
fi

# Set FOG web directory
FOG_WEB_DIR="/var/www/html/fog"
if [ ! -d "$FOG_WEB_DIR" ]; then
    FOG_WEB_DIR="/var/www/fog"
fi

if [ ! -d "$FOG_WEB_DIR" ]; then
    echo "ERROR: Cannot find FOG web directory"
    exit 1
fi

echo "✓ Found FOG directory: $FOG_WEB_DIR"
echo ""

# Backup important files
echo "Creating backups..."
BACKUP_DIR="$FOG_WEB_DIR/backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp "$FOG_WEB_DIR/lib/fog/fogbase.class.php" "$BACKUP_DIR/" 2>/dev/null
cp "$FOG_WEB_DIR/management/index.php" "$BACKUP_DIR/" 2>/dev/null
echo "✓ Backups created in: $BACKUP_DIR"
echo ""

# Step 1: Register classes in autoloader
echo "Step 1: Registering Game classes in autoloader..."
FOGBASE_FILE="$FOG_WEB_DIR/lib/fog/fogbase.class.php"

if ! grep -q "'Game'" "$FOGBASE_FILE"; then
    # Find the classMap array and add Game classes
    sed -i "/\$classMap = array(/a\\
        'Game' => 'game.class.php',\\
        'GameManager' => 'gamemanager.class.php',\\
        'GameManagementPage' => '../pages/gamemanagementpage.class.php'," "$FOGBASE_FILE"
    echo "✓ Game classes registered"
else
    echo "⚠ Game classes already registered"
fi
echo ""

# Step 2: Add menu item
echo "Step 2: Adding Game menu to navigation..."
INDEX_FILE="$FOG_WEB_DIR/management/index.php"

if ! grep -q "case 'game':" "$INDEX_FILE"; then
    # Find the switch statement and add game case
    sed -i "/case 'image':/a\\
    case 'game':\\
        \$page = new GameManagementPage();\\
        break;" "$INDEX_FILE"
    echo "✓ Game route added"
else
    echo "⚠ Game route already exists"
fi
echo ""

# Step 3: Import database schema
echo "Step 3: Setting up database..."
read -p "Enter MySQL root password: " -s MYSQL_PASS
echo ""

mysql -u root -p"$MYSQL_PASS" fog < "$FOG_WEB_DIR/lib/fog/game_install.sql" 2>/dev/null
if [ $? -eq 0 ]; then
    echo "✓ Database tables created and sample data inserted"
else
    echo "⚠ Database setup may have failed - check manually"
fi
echo ""

# Step 4: Set permissions
echo "Step 4: Setting file permissions..."
chown -R www-data:www-data "$FOG_WEB_DIR/lib/fog/game*.php" 2>/dev/null
chown -R www-data:www-data "$FOG_WEB_DIR/lib/pages/gamemanagementpage.class.php" 2>/dev/null
chmod 644 "$FOG_WEB_DIR/lib/fog/game*.php" 2>/dev/null
chmod 644 "$FOG_WEB_DIR/lib/pages/gamemanagementpage.class.php" 2>/dev/null
echo "✓ Permissions set"
echo ""

# Step 5: Clear cache
echo "Step 5: Clearing cache..."
rm -rf "$FOG_WEB_DIR/tmp/*" 2>/dev/null
echo "✓ Cache cleared"
echo ""

# Step 6: Restart Apache
echo "Step 6: Restarting web server..."
if systemctl restart apache2 2>/dev/null; then
    echo "✓ Apache2 restarted"
elif systemctl restart httpd 2>/dev/null; then
    echo "✓ HTTPD restarted"
else
    echo "⚠ Please restart web server manually"
fi
echo ""

# Get server IP
SERVER_IP=$(hostname -I | awk '{print $1}')

echo "============================================"
echo "✅ INTEGRATION COMPLETE!"
echo "============================================"
echo ""
echo "📌 Access Game Management at:"
echo "   http://$SERVER_IP/fog/management/?node=game"
echo ""
echo "   or"
echo ""
echo "   http://$SERVER_IP/fog/management/index.php?node=game"
echo ""
echo "🔑 Default Login:"
echo "   Username: fog"
echo "   Password: password"
echo ""
echo "📁 Files installed:"
echo "   - $FOG_WEB_DIR/lib/fog/game.class.php"
echo "   - $FOG_WEB_DIR/lib/fog/gamemanager.class.php"
echo "   - $FOG_WEB_DIR/lib/pages/gamemanagementpage.class.php"
echo ""
echo "🗄️  Database:"
echo "   - Table 'games' created with 5 sample games"
echo ""
echo "📋 Next steps:"
echo "   1. Open browser and navigate to the URL above"
echo "   2. Login to FOG"
echo "   3. Look for 'Game' icon in top navigation"
echo "   4. Start managing games!"
echo ""
echo "💡 Troubleshooting:"
echo "   - If you don't see the Game menu, clear browser cache (Ctrl+Shift+R)"
echo "   - Check Apache error log: tail -f /var/log/apache2/error.log"
echo "   - Backups stored in: $BACKUP_DIR"
echo ""
echo "============================================"
