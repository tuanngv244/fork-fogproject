# Test FOG Game Management API
param(
    [string]$BaseUrl = "http://localhost/fog/service"
)

Write-Host "=== Testing FOG Game Management API ===" -ForegroundColor Cyan
Write-Host ""

# Test 1: List all games
Write-Host "1. Testing GET /game.php (List all games)..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "$BaseUrl/game.php" -Method Get
    Write-Host "✓ Success!" -ForegroundColor Green
    Write-Host "   Total games: $($response.data.total)" -ForegroundColor White
    $response.data | ConvertTo-Json -Depth 3
} catch {
    Write-Host "✗ Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test 2: Create a new game
Write-Host "2. Testing POST /game.php (Create new game)..." -ForegroundColor Yellow
try {
    $newGame = @{
        name = "Counter-Strike 2"
        path = "/games/cs2"
        version = "1.0.0"
        enabled = 1
        notes = "Created via API test"
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri "$BaseUrl/game.php" `
        -Method Post `
        -Body $newGame `
        -ContentType "application/json"
    
    Write-Host "✓ Success! Game ID: $($response.data.id)" -ForegroundColor Green
    $gameId = $response.data.id
    $response | ConvertTo-Json -Depth 3
} catch {
    Write-Host "✗ Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

# Test 3: Get single game
if ($gameId) {
    Write-Host "3. Testing GET /game.php?id=$gameId (Get single game)..." -ForegroundColor Yellow
    try {
        $response = Invoke-RestMethod -Uri "$BaseUrl/game.php?id=$gameId" -Method Get
        Write-Host "✓ Success!" -ForegroundColor Green
        $response.data | ConvertTo-Json -Depth 3
    } catch {
        Write-Host "✗ Failed: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    Write-Host ""
    
    # Test 4: Update game
    Write-Host "4. Testing PUT /game.php?id=$gameId (Update game)..." -ForegroundColor Yellow
    try {
        $updateData = @{
            version = "2.0.0"
            notes = "Updated via API test"
        } | ConvertTo-Json

        $response = Invoke-RestMethod -Uri "$BaseUrl/game.php?id=$gameId" `
            -Method Put `
            -Body $updateData `
            -ContentType "application/json"
        
        Write-Host "✓ Success!" -ForegroundColor Green
        $response | ConvertTo-Json -Depth 3
    } catch {
        Write-Host "✗ Failed: $($_.Exception.Message)" -ForegroundColor Red
    }
    
    Write-Host ""
    
    # Test 5: Delete game
    Write-Host "5. Testing DELETE /game.php?id=$gameId (Delete game)..." -ForegroundColor Yellow
    try {
        $response = Invoke-RestMethod -Uri "$BaseUrl/game.php?id=$gameId" -Method Delete
        Write-Host "✓ Success!" -ForegroundColor Green
        $response | ConvertTo-Json -Depth 3
    } catch {
        Write-Host "✗ Failed: $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host ""

# Test 6: Game listing (text format)
Write-Host "6. Testing GET /gamelisting.php (Text format)..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "$BaseUrl/gamelisting.php"
    Write-Host "✓ Success!" -ForegroundColor Green
    Write-Host $response.Content -ForegroundColor White
} catch {
    Write-Host "✗ Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== API Testing Complete ===" -ForegroundColor Cyan
