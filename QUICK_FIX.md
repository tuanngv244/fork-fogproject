# ⚠️ FIX: Game Module Không Hiện trong Navigator

## Vấn đề: 
- Module không hiện trong menu
- URL `?node=game` không hoạt động

## Nguyên nhân:
Module chưa được tích hợp vào FOG core (chưa register classes và routes)

---

## ✅ GIẢI PHÁP NHANH

### Bước 1: Upload lại package (đã có patch files mới)

```bash
scp -r d:\fogproject\game_module_package root@YOUR_FOG_IP:/tmp/
```

### Bước 2: SSH vào FOG server và chạy script patch

```bash
ssh root@YOUR_FOG_IP

cd /tmp/game_module_package
chmod +x apply_patches.sh
sudo bash apply_patches.sh
```

Script này sẽ tự động:
- ✅ Thêm Game classes vào autoloader (`fogbase.class.php`)
- ✅ Thêm route `case 'game'` vào router (`index.php`)
- ✅ Set permissions
- ✅ Clear cache
- ✅ Restart Apache

### Bước 3: Clear browser cache

Trong trình duyệt: **Ctrl + Shift + R**

### Bước 4: Test

```
http://YOUR_FOG_IP/fog/management/?node=game
```

---

## 🔧 Hoặc Patch Thủ Công (nếu script fails)

### PATCH 1: Edit `/var/www/html/fog/lib/fog/fogbase.class.php`

Tìm dòng:
```php
'Image' => 'image.class.php',
'ImageManager' => 'imagemanager.class.php',
```

Thêm ngay sau đó:
```php
'Game' => 'game.class.php',
'GameManager' => 'gamemanager.class.php',
'GameManagementPage' => '../pages/gamemanagementpage.class.php',
```

### PATCH 2: Edit `/var/www/html/fog/management/index.php`

Tìm:
```php
case 'image':
    $page = new ImageManagementPage();
    break;
```

Thêm ngay sau:
```php
case 'game':
    $page = new GameManagementPage();
    break;
```

### Restart Apache:
```bash
sudo systemctl restart apache2
rm -rf /var/www/html/fog/tmp/*
```

---

## 📋 Verify Script

Chạy để kiểm tra:

```bash
# Check files
ls -la /var/www/html/fog/lib/fog/game*.php
ls -la /var/www/html/fog/lib/pages/gamemanagementpage.class.php

# Check patches
grep "Game.*game.class.php" /var/www/html/fog/lib/fog/fogbase.class.php
grep "case 'game':" /var/www/html/fog/management/index.php

# Should both return results if patched correctly
```

---

## 📁 Files Reference

Package đã được update với:
- ✅ `apply_patches.sh` - Auto patch script  
- ✅ `PATCH_1_fogbase.txt` - Chi tiết patch 1
- ✅ `PATCH_2_index.txt` - Chi tiết patch 2
- ✅ `INTEGRATION_FIX.md` - Full guide

---

**Sau khi apply patches, module SẼ HOẠT ĐỘNG ngay!**
