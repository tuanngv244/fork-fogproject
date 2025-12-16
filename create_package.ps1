# Simple Game Module Package Creator

$sourceDir = "d:\fogproject"
$outputDir = "$sourceDir\game_module_package"

Write-Host "Creating Game Module Package..." -ForegroundColor Cyan
Write-Host ""

# Create directories
if (Test-Path $outputDir) {
    Remove-Item -Path $outputDir -Recurse -Force
}
New-Item -ItemType Directory -Path "$outputDir\lib\fog" -Force | Out-Null
New-Item -ItemType Directory -Path "$outputDir\lib\pages" -Force | Out-Null

# Copy files
Copy-Item "$sourceDir\packages\web\lib\fog\game.class.php" "$outputDir\lib\fog\" -ErrorAction SilentlyContinue
Copy-Item "$sourceDir\packages\web\lib\fog\gamemanager.class.php" "$outputDir\lib\fog\" -ErrorAction SilentlyContinue
Copy-Item "$sourceDir\packages\web\lib\fog\game_install.sql" "$outputDir\lib\fog\" -ErrorAction SilentlyContinue
Copy-Item "$sourceDir\packages\web\lib\pages\gamemanagementpage.class.php" "$outputDir\lib\pages\" -ErrorAction SilentlyContinue
Copy-Item "$sourceDir\integrate_game_module.sh" "$outputDir\" -ErrorAction SilentlyContinue
Copy-Item "$sourceDir\open_game_module.html" "$outputDir\" -ErrorAction SilentlyContinue

Write-Host "[OK] Files copied" -ForegroundColor Green

# Create quick guide
$guide = @"
FOG GAME MODULE - QUICK START
=============================

INSTALLATION:
1. Upload package to FOG server:
   scp -r game_module_package root@YOUR_FOG_IP:/tmp/

2. SSH to server and run:
   cd /tmp/game_module_package
   chmod +x integrate_game_module.sh
   sudo bash integrate_game_module.sh

3. Access module:
   http://YOUR_FOG_IP/fog/management/?node=game

LOGIN:
   Username: fog
   Password: password

PACKAGE CONTENTS:
- game.class.php (Model)
- gamemanager.class.php (Manager) 
- gamemanagementpage.class.php (Controller)
- game_install.sql (Database with 5 sample games)
- integrate_game_module.sh (Auto installer)
- open_game_module.html (Quick access page)

For detailed docs, see GAME-MODULE-INTEGRATION.md

Created: December 16, 2025
"@

$guide | Out-File "$outputDir\QUICK_START.txt" -Encoding ASCII

Write-Host "[OK] Guide created" -ForegroundColor Green
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "PACKAGE READY!" -ForegroundColor Green  
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Location: $outputDir" -ForegroundColor Yellow
Write-Host ""
Write-Host "Files created:" -ForegroundColor Yellow
Get-ChildItem -Path $outputDir -Recurse -File | ForEach-Object {
    Write-Host "  - $($_.Name)" -ForegroundColor White
}
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Cyan
Write-Host "1. Upload to FOG: scp -r game_module_package root@FOG_IP:/tmp/" -ForegroundColor White
Write-Host "2. Run installer: sudo bash integrate_game_module.sh" -ForegroundColor White  
Write-Host "3. Open browser: http://FOG_IP/fog/management/?node=game" -ForegroundColor White
Write-Host ""

# Open folder and HTML
Start-Process explorer $outputDir
Start-Process "$outputDir\open_game_module.html"
