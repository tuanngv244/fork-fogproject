# PowerShell Script - FOG Game Module Integration Helper
# For Windows users to prepare files before uploading to FOG server

Write-Host "============================================" -ForegroundColor Cyan
Write-Host "FOG Game Module - File Preparation" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

$sourceDir = "d:\fogproject"
$outputDir = "$sourceDir\game_module_package"

# Create output directory
if (Test-Path $outputDir) {
    Remove-Item -Path $outputDir -Recurse -Force
}
New-Item -ItemType Directory -Path $outputDir -Force | Out-Null
New-Item -ItemType Directory -Path "$outputDir\lib\fog" -Force | Out-Null
New-Item -ItemType Directory -Path "$outputDir\lib\pages" -Force | Out-Null

Write-Host "[OK] Created package directory: $outputDir" -ForegroundColor Green
Write-Host ""

# Copy module files
Write-Host "Copying module files..." -ForegroundColor Yellow

Copy-Item "$sourceDir\packages\web\lib\fog\game.class.php" "$outputDir\lib\fog\" -Force
Copy-Item "$sourceDir\packages\web\lib\fog\gamemanager.class.php" "$outputDir\lib\fog\" -Force
Copy-Item "$sourceDir\packages\web\lib\fog\game_install.sql" "$outputDir\lib\fog\" -Force
Copy-Item "$sourceDir\packages\web\lib\pages\gamemanagementpage.class.php" "$outputDir\lib\pages\" -Force
Copy-Item "$sourceDir\integrate_game_module.sh" "$outputDir\" -Force

Write-Host "[OK] PHP classes copied" -ForegroundColor Green
Write-Host "[OK] SQL schema copied" -ForegroundColor Green
Write-Host "[OK] Integration script copied" -ForegroundColor Green
Write-Host ""

# Create installation guide
$guideContent = @"
╔═══════════════════════════════════════════════════════╗
║   FOG GAME MODULE - INSTALLATION GUIDE                ║
╔═══════════════════════════════════════════════════════╗

📦 PACKAGE CONTENTS:
├── lib/fog/
│   ├── game.class.php              (Model class)
│   ├── gamemanager.class.php       (Manager class)
│   └── game_install.sql            (Database schema)
├── lib/pages/
│   └── gamemanagementpage.class.php (Page controller)
└── integrate_game_module.sh        (Auto install script)

═══════════════════════════════════════════════════════

🚀 QUICK INSTALL (Recommended):

1. Upload entire package to FOG server:
   
   scp -r game_module_package root@YOUR_FOG_IP:/tmp/

2. SSH to FOG server:
   
   ssh root@YOUR_FOG_IP

3. Run auto-install script:
   
   cd /tmp/game_module_package
   chmod +x integrate_game_module.sh
   sudo bash integrate_game_module.sh

4. Open browser:
   
   http://YOUR_FOG_IP/fog/management/?node=game

═══════════════════════════════════════════════════════

📝 MANUAL INSTALL:

Step 1: Copy files to FOG
────────────────────────
cd /var/www/html/fog  # or /var/www/fog

cp /tmp/game_module_package/lib/fog/*.php lib/fog/
cp /tmp/game_module_package/lib/pages/*.php lib/pages/

Step 2: Import database
────────────────────────
mysql -u root -p fog < lib/fog/game_install.sql

Step 3: Register classes
────────────────────────
Edit: lib/fog/fogbase.class.php

Find: \$classMap = array(

Add these lines after it:
    'Game' => 'game.class.php',
    'GameManager' => 'gamemanager.class.php',
    'GameManagementPage' => '../pages/gamemanagementpage.class.php',

Step 4: Add route
────────────────────────
Edit: management/index.php

Find: case 'image':

Add after that block:
    case 'game':
        \$page = new GameManagementPage();
        break;

Step 5: Restart Apache
────────────────────────
sudo systemctl restart apache2

Step 6: Clear cache
────────────────────────
rm -rf /var/www/html/fog/tmp/*

Ctrl + Shift + R in browser

═══════════════════════════════════════════════════════

🌐 ACCESS MODULE:

After installation, access via:

URL: http://YOUR_FOG_IP/fog/management/?node=game

Navigation: Look for Gamepad icon (🎮) in top menu

═══════════════════════════════════════════════════════

🎮 FEATURES:

✓ List all games with details
✓ Add new game with complete form
✓ Edit game information
✓ Delete games
✓ Search and filter
✓ Statistics dashboard
✓ Sample data included (5 games)

═══════════════════════════════════════════════════════

📊 SAMPLE GAMES INCLUDED:

1. League of Legends (80 GB - Downloaded)
2. Counter-Strike 2 (32 GB - Downloaded)
3. Dota 2 (44 GB - Downloading)
4. Valorant (0 GB - Not Downloaded)
5. GTA V (100 GB - Downloaded, Protected)

═══════════════════════════════════════════════════════

🔧 TROUBLESHOOTING:

Problem: Module not appearing in menu
Solution: 
  - Clear browser cache (Ctrl + Shift + R)
  - Verify files copied correctly
  - Check Apache error log: tail -f /var/log/apache2/error.log

Problem: Database error
Solution:
  - Verify table exists: mysql -e "SHOW TABLES LIKE 'games';" fog
  - Re-import: mysql -u root -p fog < game_install.sql

Problem: "Class not found" error
Solution:
  - Check fogbase.class.php has Game classes registered
  - Restart Apache: systemctl restart apache2

═══════════════════════════════════════════════════════

📚 DOCUMENTATION:

Full docs available in:
- GAME-MODULE-INTEGRATION.md
- GAME-MODULE-TESTING.md

═══════════════════════════════════════════════════════

✅ Module developed by: GitHub Copilot AI
📅 Date: December 16, 2025
🔖 Version: 1.0.0
📄 License: GPLv3 (same as FOG Project)

═══════════════════════════════════════════════════════
"@

$guideContent | Out-File "$outputDir\INSTALL_GUIDE.txt" -Encoding UTF8

Write-Host "✓ Installation guide created" -ForegroundColor Green
Write-Host ""

# Create README
$readmeContent = @"
# FOG Game Management Module v1.0

## Quick Start

### Option 1: Auto Install (Recommended)
``````bash
scp -r game_module_package root@YOUR_FOG_IP:/tmp/
ssh root@YOUR_FOG_IP
cd /tmp/game_module_package
chmod +x integrate_game_module.sh
sudo bash integrate_game_module.sh
``````

### Option 2: Manual Install
See INSTALL_GUIDE.txt for detailed steps

## Access Module
``````
http://YOUR_FOG_IP/fog/management/?node=game
``````

## What's Included
* Game model and manager classes
* Game management page controller
* Database schema with sample data
* Auto-installation script
* Complete documentation

## Features
* Full CRUD operations
* Game state tracking
* Size management
* Server synchronization
* Run count statistics
* Protected games
* Auto-update support

Created: December 16, 2025
"@

$readmeContent | Out-File "$outputDir\README.md" -Encoding UTF8

Write-Host "OK README created" -ForegroundColor Green
Write-Host ""

# Show summary
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "[SUCCESS] PACKAGE READY!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "📁 Package location:" -ForegroundColor Yellow
Write-Host "   $outputDir" -ForegroundColor White
Write-Host ""
Write-Host "📦 Files included:" -ForegroundColor Yellow
Get-ChildItem -Path $outputDir -Recurse -File | Select-Object FullName | ForEach-Object {
    $relativePath = $_.FullName.Replace($outputDir, "")
    Write-Host "   .$relativePath" -ForegroundColor White
}
Write-Host ""
Write-Host "🚀 Next steps:" -ForegroundColor Yellow
Write-Host "   1. Upload package to FOG server:" -ForegroundColor White
Write-Host "      scp -r `"$outputDir`" root@YOUR_FOG_IP:/tmp/" -ForegroundColor Cyan
Write-Host ""
Write-Host "   2. SSH to FOG server and run:" -ForegroundColor White
Write-Host "      cd /tmp/game_module_package" -ForegroundColor Cyan
Write-Host "      chmod +x integrate_game_module.sh" -ForegroundColor Cyan
Write-Host "      sudo bash integrate_game_module.sh" -ForegroundColor Cyan
Write-Host ""
Write-Host "   3. Open in browser:" -ForegroundColor White
Write-Host "      http://YOUR_FOG_IP/fog/management/?node=game" -ForegroundColor Cyan
Write-Host ""
Write-Host "📖 Read INSTALL_GUIDE.txt for detailed instructions" -ForegroundColor Yellow
Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan

# Open the directory
explorer $outputDir
