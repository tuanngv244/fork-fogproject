# FOG Game Management - Docker Deployment

## Yêu Cầu Hệ Thống

- **Docker Desktop** for Windows
- **PowerShell 5.1+**
- **RAM**: Tối thiểu 4GB (khuyến nghị 8GB)
- **Disk**: Tối thiểu 20GB trống

## Cài Đặt Docker Desktop

1. Tải Docker Desktop: https://www.docker.com/products/docker-desktop
2. Cài đặt và khởi động Docker Desktop
3. Kiểm tra:
   ```powershell
   docker --version
   docker-compose --version
   ```

## Khởi Động FOG Server

### Cách 1: Sử dụng PowerShell Script (Khuyến nghị)

```powershell
cd d:\fogproject
.\docker-start.ps1
```

### Cách 2: Thủ công với Docker Compose

```powershell
cd d:\fogproject

# Build và start containers
docker-compose up -d --build

# Xem logs
docker-compose logs -f

# Kiểm tra status
docker-compose ps
```

## Truy Cập Services

Sau khi containers khởi động thành công (khoảng 30-60 giây):

| Service | URL | Credentials |
|---------|-----|-------------|
| FOG Web UI | http://localhost/fog | - |
| phpMyAdmin | http://localhost:8080 | root / fogpassword |
| MySQL | localhost:3306 | foguser / fogpass |
| Game API | http://localhost/fog/service/game.php | - |

## Kiểm Tra API

### PowerShell Test Script

```powershell
.\test-api.ps1
```

### Thủ công với curl hoặc Invoke-RestMethod

```powershell
# List all games
Invoke-RestMethod -Uri "http://localhost/fog/service/game.php"

# Create new game
$body = @{
    name = "Valorant"
    path = "/games/valorant"
    version = "1.0"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/fog/service/game.php" `
    -Method Post `
    -Body $body `
    -ContentType "application/json"

# Get single game
Invoke-RestMethod -Uri "http://localhost/fog/service/game.php?id=1"

# Update game
$update = @{
    version = "2.0"
    enabled = 1
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/fog/service/game.php?id=1" `
    -Method Put `
    -Body $update `
    -ContentType "application/json"

# Delete game
Invoke-RestMethod -Uri "http://localhost/fog/service/game.php?id=1" `
    -Method Delete
```

## Quản Lý Containers

### Xem logs

```powershell
# Tất cả services
docker-compose logs -f

# Chỉ FOG server
docker-compose logs -f fogserver

# Chỉ MySQL
docker-compose logs -f mysql
```

### Khởi động lại

```powershell
# Restart tất cả
docker-compose restart

# Restart FOG server
docker-compose restart fogserver
```

### Dừng containers

```powershell
# Dừng nhưng giữ data
docker-compose stop

# Dừng và xóa containers (giữ volumes)
docker-compose down

# Dừng và xóa tất cả (bao gồm volumes - MẤT DATA!)
docker-compose down -v
```

### Rebuild containers

```powershell
docker-compose up -d --build --force-recreate
```

## Truy Cập MySQL

### Qua phpMyAdmin
- Mở http://localhost:8080
- Login: root / fogpassword

### Qua command line

```powershell
# Vào MySQL container
docker exec -it fog-mysql mysql -u root -pfogpassword fog

# Hoặc từ máy host (nếu có MySQL client)
mysql -h 127.0.0.1 -P 3306 -u foguser -pfogpass fog
```

### Kiểm tra tables

```sql
USE fog;
SHOW TABLES LIKE 'game%';
SELECT * FROM games;
SELECT * FROM game_sync_tasks;
```

## Troubleshooting

### Container không start

```powershell
# Xem logs chi tiết
docker-compose logs

# Kiểm tra resource
docker stats

# Rebuild từ đầu
docker-compose down -v
docker-compose up -d --build
```

### Port đã được sử dụng

Nếu port 80, 3306, hoặc 8080 đã được dùng, sửa `docker-compose.yml`:

```yaml
ports:
  - "8000:80"      # Thay vì 80:80
  - "3307:3306"    # Thay vì 3306:3306
  - "8081:80"      # Thay vì 8080:80 cho phpMyAdmin
```

### Database không khởi tạo

```powershell
# Xóa volume và rebuild
docker-compose down -v
docker volume rm fogproject_mysql-data
docker-compose up -d
```

### FOG Web UI lỗi 500

```powershell
# Kiểm tra permissions
docker exec -it fog-server chown -R www-data:www-data /var/www/html/fog

# Kiểm tra config
docker exec -it fog-server cat /var/www/html/fog/lib/fog/config.class.php

# Restart container
docker-compose restart fogserver
```

### API không hoạt động

```powershell
# Kiểm tra file tồn tại
docker exec -it fog-server ls -la /var/www/html/fog/service/game.php

# Kiểm tra PHP errors
docker-compose logs fogserver | Select-String "error"

# Test từ bên trong container
docker exec -it fog-server curl http://localhost/fog/service/game.php
```

## Backup và Restore

### Backup Database

```powershell
# Backup toàn bộ database
docker exec fog-mysql mysqldump -u root -pfogpassword fog > backup-$(Get-Date -Format 'yyyyMMdd').sql

# Backup chỉ game tables
docker exec fog-mysql mysqldump -u root -pfogpassword fog games gameTypes gameHostAssociation gameGroupAssociation game_sync_tasks game_sync_queue > backup-games.sql
```

### Restore Database

```powershell
# Restore từ file
Get-Content backup.sql | docker exec -i fog-mysql mysql -u root -pfogpassword fog
```

### Backup Images/Data

```powershell
# Tạo backup của image data
docker run --rm -v fogproject_images-data:/data -v ${PWD}:/backup ubuntu tar czf /backup/images-backup.tar.gz /data
```

## Production Deployment

### Thay đổi passwords

Sửa `docker-compose.yml` và `config.class.php`:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: <strong-password>
  MYSQL_PASSWORD: <strong-password>
```

### Enable SSL

Thêm vào `Dockerfile`:

```dockerfile
RUN a2enmod ssl
COPY ssl/cert.pem /etc/ssl/certs/fog.crt
COPY ssl/key.pem /etc/ssl/private/fog.key
```

### Resource limits

Thêm vào `docker-compose.yml`:

```yaml
services:
  fogserver:
    deploy:
      resources:
        limits:
          cpus: '2.0'
          memory: 2G
        reservations:
          cpus: '1.0'
          memory: 1G
```

## Uninstall

```powershell
# Dừng và xóa tất cả
docker-compose down -v

# Xóa images
docker rmi fogproject_fogserver
docker rmi mariadb:10.6
docker rmi phpmyadmin/phpmyadmin

# Xóa networks
docker network prune
```

## Support

- FOG Project: https://fogproject.org
- Docker Documentation: https://docs.docker.com
- GitHub Issues: <your-repo>/issues
