# 🎮 Cách Mở Game Module trong Web

## ✅ PACKAGE ĐÃ TẠO XONG!

Location: `d:\fogproject\game_module_package\`

---

## 🚀 3 Bước Để Mở Module

### **Bước 1: Upload lên FOG Server**

```bash
# Từ Windows (PowerShell hoặc CMD)
scp -r d:\fogproject\game_module_package root@192.168.1.100:/tmp/

# Thay 192.168.1.100 bằng IP FOG Server của bạn
```

**Hoặc dùng WinSCP:**
- Mở WinSCP
- Connect tới FOG Server
- Upload thư mục `game_module_package` vào `/tmp/`

---

### **Bước 2: Cài Đặt Module**

SSH vào FOG Server và chạy:

```bash
ssh root@192.168.1.100

cd /tmp/game_module_package
chmod +x integrate_game_module.sh
sudo bash integrate_game_module.sh
```

Script sẽ tự động:
- ✅ Import database (bảng `games` + 5 game mẫu)
- ✅ Copy files vào `/var/www/html/fog/`
- ✅ Register classes trong autoloader
- ✅ Thêm route vào index.php
- ✅ Set permissions
- ✅ Clear cache
- ✅ Restart Apache

---

### **Bước 3: Mở Trong Trình Duyệt**

```
http://192.168.1.100/fog/management/?node=game
```

**Hoặc:**

1. Đăng nhập FOG Dashboard: `http://192.168.1.100/fog/management/`
2. Tìm icon **Gamepad (🎮)** trên menu top
3. Click vào để mở Game Management

---

## 🔑 Login Information

```
Username: fog
Password: password
```

⚠️ **Đổi password sau khi đăng nhập lần đầu!**

---

## 📊 Module Dashboard

Sau khi mở module, bạn sẽ thấy:

### **Left Menu:**
- List All Games
- Create New Game
- Export Games
- Import Games

### **Main Dashboard:**
- Total Games: 5
- Total Size: ~274 GB
- Downloaded: 3
- Downloading: 1

### **Games Table:**
| ID | Name | State | Size | Path |
|----|------|-------|------|------|
| 1 | League of Legends | Downloaded | 80 GB | D:\Games\... |
| 2 | Counter-Strike 2 | Downloaded | 32 GB | D:\Games\... |
| 3 | Dota 2 | Downloading | 44 GB | D:\Games\... |
| 4 | Valorant | Not Downloaded | 0 GB | D:\Games\... |
| 5 | GTA V | Downloaded | 100 GB | D:\Games\... |

---

## 🎯 Quick Actions

### Add New Game:
1. Click "Create New Game" (left menu)
2. Fill form:
   - **Game Name*** (required)
   - **Download Path*** (required)
   - Icon, Executable, Parameters, etc.
3. Click "Add"

### Edit Game:
1. Click on game name in table
2. Modify fields
3. Click "Update"

### Delete Game:
1. Check checkbox next to game
2. Click "Delete Selected" button
3. Confirm

---

## 🐛 Troubleshooting

### Module không hiện?

```bash
# Clear browser cache
Ctrl + Shift + R

# Kiểm tra Apache error log
tail -f /var/log/apache2/error.log

# Restart Apache
sudo systemctl restart apache2
```

### Database error?

```bash
# Verify table exists
mysql -u root -p -e "SHOW TABLES LIKE 'games';" fog

# Re-import if needed
mysql -u root -p fog < /tmp/game_module_package/lib/fog/game_install.sql
```

### Class not found?

```bash
# Check files exist
ls -la /var/www/html/fog/lib/fog/game*.php
ls -la /var/www/html/fog/lib/pages/gamemanagementpage.class.php

# Verify permissions
sudo chmod 644 /var/www/html/fog/lib/fog/game*.php
sudo chmod 644 /var/www/html/fog/lib/pages/gamemanagementpage.class.php

# Restart Apache
sudo systemctl restart apache2
```

---

## 📱 Alternative Access Methods

### Method 1: Direct URL
```
http://YOUR_FOG_IP/fog/management/index.php?node=game
```

### Method 2: Quick Access HTML
Mở file: `d:\fogproject\game_module_package\open_game_module.html`
- Nhập FOG Server IP
- Click "Open Game Management"

### Method 3: FOG Dashboard Menu
- Login FOG
- Top menu → Gamepad icon
- Click to open

---

## ✅ Verification Checklist

- [ ] Package uploaded to FOG server
- [ ] Integration script executed successfully
- [ ] Database table `games` exists
- [ ] 5 sample games showing in table
- [ ] Can add new game
- [ ] Can edit game
- [ ] Can delete game
- [ ] Can search games

---

## 📚 Documentation Files

- **GAME-MODULE-INTEGRATION.md** - Chi tiết integration
- **GAME-MODULE-TESTING.md** - Test cases
- **QUICK_START.txt** - Quick reference

---

## 🎉 Success Indicators

Khi module chạy đúng, bạn sẽ thấy:

✅ URL: `http://FOG_IP/fog/management/?node=game` loads without error
✅ Title: "Game Management" appears
✅ Table shows 5 games with green checkmarks
✅ Can click "Create New Game" and form appears
✅ Statistics show: Total Games: 5, Total Size: ~274 GB

---

**Module Created:** December 16, 2025  
**Status:** ✅ Ready to Install  
**Next Action:** Follow Step 1 above to upload and install

---

## 🆘 Need Help?

Check these files for more info:
- `GAME-MODULE-INTEGRATION.md` - Full installation guide
- `GAME-MODULE-TESTING.md` - Testing procedures
- `integrate_game_module.sh` - Auto installer script

Or check FOG error logs:
```bash
tail -f /var/log/apache2/error.log
tail -f /opt/fog/log/error.log
```
