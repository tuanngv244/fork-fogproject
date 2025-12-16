# Hướng dẫn Tích hợp Module Game Management vào FOG

## 📦 Files đã tạo

### 1. Model Classes (Data Layer)
```
✅ packages/web/lib/fog/game.class.php
✅ packages/web/lib/fog/gamemanager.class.php
```

### 2. Page Controller (UI Layer)
```
✅ packages/web/lib/pages/gamemanagementpage.class.php
```

### 3. Database Schema
```
✅ packages/web/lib/fog/game_schema.sql
```

---

## 🚀 Các Bước Cài Đặt

### Bước 1: Import Database Schema

```bash
# SSH vào FOG server
ssh root@your-fog-server

# Import SQL schema
mysql -u root -p fog < /var/www/html/fog/lib/fog/game_schema.sql

# Hoặc thủ công:
mysql -u root -p
USE fog;
SOURCE /var/www/html/fog/lib/fog/game_schema.sql;
```

### Bước 2: Copy Files vào FOG Installation

```bash
# Trên Windows (từ thư mục fogproject)
# Copy sang FOG server qua WinSCP hoặc:

# Trên FOG Server:
cd /var/www/html/fog  # hoặc /var/www/fog

# Copy model classes
cp /path/to/game.class.php lib/fog/
cp /path/to/gamemanager.class.php lib/fog/

# Copy page controller
cp /path/to/gamemanagementpage.class.php lib/pages/
```

### Bước 3: Register Classes trong Autoloader

Mở file: `/var/www/html/fog/lib/fog/fogbase.class.php`

Tìm section `FOGCore::$classMap` (khoảng dòng 100-300) và thêm:

```php
'Game' => 'game.class.php',
'GameManager' => 'gamemanager.class.php',
'GameManagementPage' => '../pages/gamemanagementpage.class.php',
```

### Bước 4: Thêm Menu Navigation

Mở file: `/var/www/html/fog/lib/fog/fogpage.class.php`

Tìm method `__construct()` hoặc section định nghĩa main menu, thêm:

```php
// Thêm vào $this->menu array (khoảng dòng 150-200)
'game' => array(
    'title' => _('Game'),
    'icon' => 'fa fa-gamepad',
),
```

### Bước 5: Register Route

Mở file: `/var/www/html/fog/management/index.php`

Tìm switch case hoặc routing logic (khoảng dòng 50-100) và thêm:

```php
case 'game':
    $page = new GameManagementPage();
    break;
```

### Bước 6: Clear Cache và Restart Apache

```bash
# Clear FOG cache
rm -rf /var/www/html/fog/tmp/*

# Clear PHP opcache
sudo systemctl restart apache2  # Ubuntu/Debian
# hoặc
sudo systemctl restart httpd    # CentOS/RHEL

# Clear browser cache
Ctrl + Shift + R
```

---

## 🎯 Truy cập Module

Sau khi cài đặt, truy cập:

```
http://your-fog-server/fog/management/index.php?node=game
```

Bạn sẽ thấy:
- **Menu bên trái**: Gamepad icon → "Game"
- **Các trang**:
  - List All Games
  - Create New Game
  - Edit Game
  - Delete Game

---

## 📋 Cấu trúc Database Table `games`

| Field | Type | Description |
|-------|------|-------------|
| `gameID` | INT | Primary key, auto increment |
| `gameName` | VARCHAR(255) | Tên game (required, unique) |
| `gameDesc` | LONGTEXT | Mô tả game |
| `gameIcon` | LONGTEXT | URL/path đến icon |
| `gameDownloadPath` | LONGTEXT | Đường dẫn download (required) |
| `gameExecutable` | VARCHAR(255) | File .exe để chạy |
| `gameParameters` | LONGTEXT | Tham số khởi chạy |
| `gameArchivePath` | LONGTEXT | Đường dẫn archive/backup |
| `gameSyncServer` | VARCHAR(100) | Server đồng bộ (như VANTUAN) |
| `gameDriveLetter` | VARCHAR(10) | Ổ đĩa (D:, E:, etc) |
| `gameSize` | BIGINT | Kích thước (bytes) |
| `gameState` | INT | 0-6 (Not Downloaded → Error) |
| `gameLastUpdate` | DATETIME | Lần update cuối |
| `gameLocalUpdateTime` | DATETIME | Local update time |
| `gameRunCount` | INT | Số lần chạy |
| `gameLastRunTime` | DATETIME | Lần chạy cuối |
| `gameDateTime` | TIMESTAMP | Ngày tạo |
| `gameCreateBy` | VARCHAR(40) | Người tạo |
| `gameProtect` | ENUM | 0=unlocked, 1=locked |
| `gameEnabled` | ENUM | 0=disabled, 1=enabled |
| `gameAutoUpdate` | ENUM | 0=no, 1=yes |

---

## 🎨 Các Tính năng Chính

### 1. List Games (Dashboard)
- Hiển thị tất cả games trong table
- Columns: ID, Name, State, Update, Size, Path, Run Count
- Icons: Protected/Unprotected, Enabled/Disabled
- Search và filter

### 2. Add New Game
Form với các fields:
- ✅ Game Icon (text input cho URL)
- ✅ Game Name* (required)
- ✅ Description (textarea)
- ✅ Download Path* (required)
- ✅ Executable (file name)
- ✅ Parameters (command line args)
- ✅ Archive Path
- ✅ Sync Server (dropdown/text)
- ✅ Drive Letter (D:, E:, etc)
- ✅ State (dropdown: Not Downloaded, Downloading, Downloaded, etc)
- ✅ Protected (checkbox)
- ✅ Enabled (checkbox)
- ✅ Auto Update (checkbox)

### 3. Edit Game
- Giống Add form nhưng pre-filled với data
- Update button thay vì Add

### 4. Delete Game
- Xóa game khỏi database
- Có confirmation dialog

### 5. Statistics
- Total Games
- Total Size
- Downloaded count
- Downloading count

---

## 🔧 Customization

### Thêm Field Mới

**1. Update Database:**
```sql
ALTER TABLE `games` ADD COLUMN `gameGenre` VARCHAR(100) AFTER `gameDesc`;
```

**2. Update Model (game.class.php):**
```php
protected $databaseFields = array(
    // ... existing fields
    'genre' => 'gameGenre',
);
```

**3. Update Page (gamemanagementpage.class.php):**
```php
// Trong $fields array của gameForm()
'<label for="game-genre">Genre</label>' => self::getClass('GameManager')
    ->inputText('genre', $this->obj->get('genre')),
```

### Thêm State Mới

```php
// Trong getStateDisplay() method của Game class
$states = array(
    // ... existing states
    7 => _('Paused'),
    8 => _('Queued'),
);
```

### Customize Table Display

Sửa trong `gamemanagementpage.class.php`:

```php
// Ẩn cột Run Count
// Xóa từ $this->headerData và $this->templates

// Thêm cột mới
$this->headerData[] = _('Genre');
$this->templates[] = '${genre}';
```

---

## 🐛 Troubleshooting

### Lỗi: "Class 'Game' not found"
```bash
# Kiểm tra file paths
ls -la /var/www/html/fog/lib/fog/game*.php

# Clear PHP opcache
sudo systemctl restart apache2
```

### Lỗi: "Table 'fog.games' doesn't exist"
```bash
# Import lại schema
mysql -u root -p fog < game_schema.sql

# Verify table
mysql -u root -p -e "SHOW TABLES LIKE 'games';" fog
```

### Lỗi: Menu không hiện
```bash
# Check browser console (F12)
# Clear cache: Ctrl + Shift + R
# Check Apache error log
tail -f /var/log/apache2/error.log
```

### Form không submit
```bash
# Check permissions
chmod 644 /var/www/html/fog/lib/pages/gamemanagementpage.class.php

# Check PHP errors
tail -f /var/log/apache2/error.log
```

---

## 📝 API Endpoints (Tự động từ FOG Framework)

Sau khi tích hợp, bạn có thể dùng API:

```bash
# List all games
curl http://fog-server/fog/game

# Get game by ID
curl http://fog-server/fog/game/1

# Create game (POST)
curl -X POST http://fog-server/fog/game \
  -d "name=New Game" \
  -d "downloadPath=D:\\Games\\NewGame"

# Update game (PUT)
curl -X PUT http://fog-server/fog/game/1 \
  -d "state=2"

# Delete game
curl -X DELETE http://fog-server/fog/game/1
```

---

## ✅ Checklist Hoàn Thành

- [x] Tạo Model classes (Game, GameManager)
- [x] Tạo Page controller (GameManagementPage)
- [x] Tạo Database schema
- [x] Import SQL vào database
- [x] Copy files vào FOG installation
- [x] Register classes trong autoloader
- [x] Thêm menu navigation
- [x] Register route
- [x] Clear cache & restart Apache
- [x] Test truy cập module
- [x] Test CRUD operations

---

## 🎓 Tham khảo Thêm

### Files FOG gốc để tham khảo:
- `packages/web/lib/fog/image.class.php` - Model pattern
- `packages/web/lib/pages/imagemanagementpage.class.php` - Page pattern
- `packages/web/lib/fog/fogcontroller.class.php` - Base controller
- `packages/web/lib/fog/fogpage.class.php` - Base page

### FOG Framework Methods:
- `inputText()` - Text input field
- `inputTextArea()` - Textarea field
- `inputSelect()` - Dropdown select
- `formatByteSize()` - Format size
- `formatTime()` - Format datetime
- `validDate()` - Validate date

---

**Module được thiết kế theo đúng chuẩn FOG Framework, tương thích với phiên bản 1.5.10+**

Tạo bởi: GitHub Copilot AI
Ngày: 16/12/2025
