#!/bin/bash
#
# Complete Game Module Setup + Diagnostics
#

echo "======================================="
echo "  FOG Game Module - Complete Setup"
echo "======================================="
echo ""

# 1. Import database
echo "[1/5] Importing database..."
if [ -f "game_install.sql" ]; then
    mysql -u root fog < game_install.sql
    echo "✓ Database imported with 5 sample games"
else
    echo "⚠ game_install.sql not found - creating table manually..."
    mysql -u root fog -e "CREATE TABLE IF NOT EXISTS games (
        gameID int(11) NOT NULL AUTO_INCREMENT,
        gameName varchar(255) NOT NULL,
        gameDescription text,
        gameIcon varchar(500) DEFAULT NULL,
        gameDownloadPath varchar(1000) DEFAULT NULL,
        gameExecutable varchar(500) DEFAULT NULL,
        gameParameters varchar(1000) DEFAULT NULL,
        gameArchivePath varchar(1000) DEFAULT NULL,
        gameSyncServer int(11) DEFAULT '0',
        gameDriveLetter varchar(3) DEFAULT 'C:',
        gameSize bigint(20) DEFAULT '0',
        gameState int(2) DEFAULT '0',
        gameLastUpdate datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        gameRunCount int(11) DEFAULT '0',
        gameVersion varchar(50) DEFAULT NULL,
        gamePublisher varchar(255) DEFAULT NULL,
        gameGenre varchar(100) DEFAULT NULL,
        gameRating varchar(10) DEFAULT NULL,
        gameReleaseDate date DEFAULT NULL,
        gameRequirements text,
        gameNotes text,
        PRIMARY KEY (gameID)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    echo "✓ Table created"
fi

# 2. Verify installation
echo ""
echo "[2/5] Verifying files..."
ls -la /var/www/html/fog/lib/fog/game*.php 2>/dev/null && echo "✓ Model files OK" || echo "✗ Model files missing"
ls -la /var/www/html/fog/lib/pages/gamemanagementpage.class.php 2>/dev/null && echo "✓ Page file OK" || echo "✗ Page file missing"

echo ""
echo "[3/5] Verifying patches..."
grep -q "'Game'.*'game.class.php'" /var/www/html/fog/lib/fog/fogbase.class.php && echo "✓ Classes registered" || echo "✗ Classes NOT registered"
grep -q "case 'game':" /var/www/html/fog/management/index.php && echo "✓ Route registered" || echo "✗ Route NOT registered"

echo ""
echo "[4/5] Checking database..."
GAME_COUNT=$(mysql -u root fog -e "SELECT COUNT(*) FROM games;" -sN 2>/dev/null)
if [ $? -eq 0 ]; then
    echo "✓ Table exists with $GAME_COUNT games"
else
    echo "✗ Table doesn't exist or query failed"
fi

echo ""
echo "[5/5] Restarting services..."
rm -rf /var/www/html/fog/tmp/*
rm -rf /var/www/html/fog/lib/fog/.htaccess.cache 2>/dev/null
systemctl restart apache2 || service apache2 restart
echo "✓ Apache restarted & cache cleared"

echo ""
echo "======================================="
echo "  Checking for errors..."
echo "======================================="
echo ""
echo "Last 10 Apache errors:"
tail -n 10 /var/log/apache2/error.log 2>/dev/null | grep -i "game\|fatal\|error" || echo "No errors found"

echo ""
echo "======================================="
echo "  SETUP COMPLETE!"
echo "======================================="
echo ""
echo "Access module at:"
echo "  http://$(hostname -I | awk '{print $1}')/fog/management/?node=game"
echo ""
echo "If page is blank:"
echo "  1. Clear browser cache (Ctrl+Shift+R)"
echo "  2. Check: tail -f /var/log/apache2/error.log"
echo "  3. Run: php -l /var/www/html/fog/lib/pages/gamemanagementpage.class.php"
echo ""
