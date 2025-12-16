# FOG Game Management - Docker Startup Script
# Khởi động FOG server với Docker Compose

Write-Host "=== FOG Game Management Docker Setup ===" -ForegroundColor Cyan
Write-Host ""

# Kiểm tra Docker
Write-Host "Checking Docker installation..." -ForegroundColor Yellow
try {
    $dockerVersion = docker --version
    Write-Host "✓ Docker installed: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Docker not found! Please install Docker Desktop." -ForegroundColor Red
    exit 1
}

# Kiểm tra Docker Compose
try {
    $composeVersion = docker-compose --version
    Write-Host "✓ Docker Compose installed: $composeVersion" -ForegroundColor Green
} catch {
    Write-Host "✗ Docker Compose not found!" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Building and starting containers..." -ForegroundColor Yellow

# Stop existing containers
docker-compose down 2>$null

# Build và start
docker-compose up -d --build

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "=== FOG Server Started Successfully! ===" -ForegroundColor Green
    Write-Host ""
    Write-Host "Services:" -ForegroundColor Cyan
    Write-Host "  FOG Web UI:    http://localhost/fog" -ForegroundColor White
    Write-Host "  phpMyAdmin:    http://localhost:8080" -ForegroundColor White
    Write-Host "  MySQL Port:    localhost:3306" -ForegroundColor White
    Write-Host ""
    Write-Host "Credentials:" -ForegroundColor Cyan
    Write-Host "  MySQL Root:    root / fogpassword" -ForegroundColor White
    Write-Host "  MySQL User:    foguser / fogpass" -ForegroundColor White
    Write-Host "  Database:      fog" -ForegroundColor White
    Write-Host ""
    Write-Host "Game Management API:" -ForegroundColor Cyan
    Write-Host "  List Games:    GET  http://localhost/fog/service/game.php" -ForegroundColor White
    Write-Host "  Create Game:   POST http://localhost/fog/service/game.php" -ForegroundColor White
    Write-Host "  Update Game:   PUT  http://localhost/fog/service/game.php?id=X" -ForegroundColor White
    Write-Host "  Delete Game:   DELETE http://localhost/fog/service/game.php?id=X" -ForegroundColor White
    Write-Host ""
    Write-Host "Checking container status..." -ForegroundColor Yellow
    Start-Sleep -Seconds 5
    docker-compose ps
    
    Write-Host ""
    Write-Host "Waiting for services to be ready..." -ForegroundColor Yellow
    $maxWait = 60
    $waited = 0
    
    while ($waited -lt $maxWait) {
        try {
            $response = Invoke-WebRequest -Uri "http://localhost/fog/" -TimeoutSec 2 -ErrorAction SilentlyContinue
            if ($response.StatusCode -eq 200) {
                Write-Host "✓ FOG Web UI is ready!" -ForegroundColor Green
                break
            }
        } catch {
            Write-Host "." -NoNewline
            Start-Sleep -Seconds 2
            $waited += 2
        }
    }
    
    Write-Host ""
    Write-Host ""
    Write-Host "View logs with:" -ForegroundColor Cyan
    Write-Host "  docker-compose logs -f" -ForegroundColor White
    Write-Host ""
    Write-Host "Stop containers with:" -ForegroundColor Cyan
    Write-Host "  docker-compose down" -ForegroundColor White
    Write-Host ""
    
} else {
    Write-Host ""
    Write-Host "✗ Failed to start containers!" -ForegroundColor Red
    Write-Host "Check logs with: docker-compose logs" -ForegroundColor Yellow
    exit 1
}
