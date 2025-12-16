# ✅ FOG Docker đã chạy thành công!

## Truy cập ngay:

### 🌐 Web UI
- **FOG Management**: http://localhost/fog/management
- **phpMyAdmin**: http://localhost:8080
  - User: `root`
  - Pass: `fogpassword`

### 🎮 Game Management API
```powershell
# List all games
Invoke-RestMethod -Uri "http://localhost/fog/service/game.php"

# Create a new game
$game = @{
    name = "Counter-Strike 2"
    path = "/games/cs2"
    version = "1.0.0"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/fog/service/game.php" `
    -Method Post `
    -Body $game `
    -ContentType "application/json"
```

## Quản lý Containers

### Xem logs
```powershell
docker-compose logs -f fogserver
```

### Restart
```powershell
docker-compose restart
```

### Dừng
```powershell
docker-compose down
```

### Start lại
```powershell
docker-compose up -d
```

## Test API đầy đủ
```powershell
.\test-api.ps1
```

## Troubleshooting

### Nếu bị lỗi "Not Found"
```powershell
docker-compose restart fogserver
Start-Sleep -Seconds 10
```

### Xem MySQL data
```powershell
docker exec -it fog-mysql mysql -u root -pfogpassword fog
```

```sql
SHOW TABLES;
SELECT * FROM games;
```

### Rebuild toàn bộ
```powershell
docker-compose down -v
docker-compose up -d --build
```

## Files quan trọng

- **Dockerfile**: Image configuration
- **docker-compose.yml**: Services orchestration
- **config.class.php**: FOG database config
- **game_schema.sql**: Database schema
- **packages/web/service/game.php**: REST API
- **packages/web/lib/fog/game*.class.php**: Game classes

## Endpoints Summary

| Method | URL | Description |
|--------|-----|-------------|
| GET | /fog/service/game.php | List all games |
| GET | /fog/service/game.php?id=X | Get single game |
| POST | /fog/service/game.php | Create game |
| PUT | /fog/service/game.php?id=X | Update game |
| DELETE | /fog/service/game.php?id=X | Delete game |
| GET | /fog/service/game.php?action=hosts&hostID=X | Games for host |
| GET | /fog/service/game.php?action=groups&groupID=X | Games for group |

Enjoy! 🚀
