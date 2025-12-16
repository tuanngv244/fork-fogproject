# Hướng dẫn Render UI trong FOG Project

## 📍 File Render Trang "Image Management" (Ảnh của bạn)

### Luồng xử lý Request:

```
Browser Request
    ↓
http://fogserver/fog/management/index.php?node=image
    ↓
packages/web/management/index.php (Router)
    ↓
packages/web/lib/pages/imagemanagementpage.class.php (Controller)
    ↓
Render HTML Table với data từ MySQL
```

---

## 🗂️ Các File Chính để Render UI

### 1. **Entry Point (Điểm vào)**
```
📁 packages/web/management/index.php
```
- File chính khi bạn truy cập `/fog/management`
- Xử lý routing dựa trên parameter `?node=image`

### 2. **Page Controller (Xử lý trang Image Management)**
```
📁 packages/web/lib/pages/imagemanagementpage.class.php
```
**Chức năng:**
- Định nghĩa menu "Image Management"
- Tạo table headers (Image Name, Storage Group, Image Size, Captured)
- Define templates cho mỗi row
- Xử lý các actions: List, Create, Edit, Delete

**Các method quan trọng:**
- `__construct()`: Khởi tạo menu, headers, templates
- `index()`: Render trang danh sách images (dòng 200+)
- `search()`: Xử lý search/filter
- `add()`: Form tạo image mới
- `edit()`: Form chỉnh sửa image

### 3. **Model (Data Layer)**
```
📁 packages/web/lib/fog/image.class.php
📁 packages/web/lib/fog/imagemanager.class.php
```
- `Image`: Object đại diện cho 1 image
- `ImageManager`: Quản lý collection của images, query database

### 4. **Base Classes (Lớp nền tảng)**
```
📁 packages/web/lib/fog/fogpage.class.php
📁 packages/web/lib/fog/fogcontroller.class.php
```
- Cung cấp các method chung cho tất cả pages
- Xử lý rendering tables, forms

---

## 🎨 Cách Render HTML Table

### Trong `imagemanagementpage.class.php`:

```php
// 1. Định nghĩa Headers (dòng ~120)
$this->headerData = array(
    '',
    '',
    '<label for="toggler">...</label>',  // Checkbox
    _('Image Name'),                      // Tên cột
    _('Storage Group'),
    _('Image Size: ON CLIENT'),
    _('Captured')
);

// 2. Định nghĩa Templates cho mỗi row (dòng ~150)
$this->templates = array(
    '${protected}',      // Icon protected
    '${enabled}',        // Icon enabled (checkmark xanh)
    '<input type="checkbox".../>',  // Checkbox
    '<a href="?node=image&sub=edit&id=${id}">${name}</a>',  // Link edit
    '${storageGroup}',
    '${size}',
    '${deployed}'
);

// 3. Lấy data từ database và render
foreach ($Images as $Image) {
    $this->data[] = array(
        'protected' => $Image->get('protected') ? 'icon' : '',
        'enabled' => $Image->get('isEnabled') ? 'checkmark' : '',
        'id' => $Image->get('id'),
        'name' => $Image->get('name'),
        'storageGroup' => $Image->getStorageGroup()->get('name'),
        'size' => $Image->get('size'),
        'deployed' => $Image->get('deployed')
    );
}

// 4. Render table
$this->render();  // Method từ FOGPage base class
```

---

## 🔍 Để Xem/Chỉnh Sửa UI

### A. Xem UI "List All Images" (như ảnh của bạn):

```php
📂 Mở file:
packages/web/lib/pages/imagemanagementpage.class.php

🔎 Tìm method: index()  (khoảng dòng 220-350)

💡 Đây là nơi render bảng danh sách images
```

### B. Thêm/Sửa cột trong bảng:

**Ví dụ: Thêm cột "OS Type"**

```php
// Bước 1: Thêm vào headerData (dòng ~120)
$this->headerData = array(
    // ... existing columns
    _('Image Size: ON CLIENT'),
    _('OS Type'),  // ← CỘT MỚI
    _('Captured')
);

// Bước 2: Thêm vào templates (dòng ~150)
$this->templates = array(
    // ... existing templates
    '${size}',
    '${osType}',  // ← TEMPLATE MỚI
    '${deployed}'
);

// Bước 3: Thêm data vào render loop
$this->data[] = array(
    // ... existing data
    'size' => $Image->get('size'),
    'osType' => $Image->get('osname'),  // ← DATA MỚI
    'deployed' => $Image->get('deployed')
);
```

### C. Thay đổi CSS/Style:

```php
📂 Mở file:
packages/web/management/css/

📄 Các file CSS chính:
- fog.css: Style chung
- dark.min.css: Theme tối
```

### D. Thay đổi Menu bên trái:

```php
📂 Mở file:
packages/web/lib/pages/imagemanagementpage.class.php

🔎 Trong __construct(), tìm:

$this->menu = array(
    'list' => _('List All Images'),
    'new' => _('Create New Image'),
    'export' => _('Export Images'),
    'import' => _('Import Images'),
    'multicast' => _('Multicast Image')
);

💡 Thêm/sửa các menu items ở đây
```

---

## 🚀 Workflow để Test Changes

### 1. **Sửa file PHP:**
```bash
cd /var/www/html/fog  # hoặc /var/www/fog
nano lib/pages/imagemanagementpage.class.php
```

### 2. **Clear cache (nếu cần):**
```bash
# Clear FOG cache
rm -rf /var/www/html/fog/tmp/*

# Clear PHP opcache
sudo systemctl restart apache2  # Ubuntu
# hoặc
sudo systemctl restart httpd    # CentOS
```

### 3. **Refresh trình duyệt:**
```
Ctrl + Shift + R  (Hard refresh)
```

### 4. **Check logs nếu lỗi:**
```bash
# Apache error log
tail -f /var/log/apache2/error.log

# FOG error log
tail -f /opt/fog/log/error.log

# PHP error log
tail -f /var/log/php_errors.log
```

---

## 📁 Mapping UI Elements → Source Files

### Từ screenshot của bạn:

| UI Element | Source File | Line/Method |
|------------|-------------|-------------|
| **"Image Management" title** | `imagemanagementpage.class.php` | Line 42 |
| **Left Menu (List/Create/Export/Import)** | `imagemanagementpage.class.php` | `__construct()` |
| **Table Headers** | `imagemanagementpage.class.php` | Line ~120 `headerData` |
| **"test - 1" row** | `imagemanagementpage.class.php` | `index()` method |
| **Green checkmark icon** | Template: `${enabled}` | CSS in `fog.css` |
| **"0.00 iB" size** | `image.class.php` | `get('size')` |
| **"Invalid date"** | `image.class.php` | `get('deployed')` |
| **"Delete" button** | `imagemanagementpage.class.php` | Line ~300+ |

---

## 🔧 Debug Tips

### Enable PHP Error Display:
```bash
sudo nano /etc/php/8.0/apache2/php.ini

# Sửa:
display_errors = On
error_reporting = E_ALL

# Restart Apache
sudo systemctl restart apache2
```

### View SQL Queries:
```php
// Thêm vào imagemanagementpage.class.php
error_log("SQL Query: " . $query);
```

### Add Debug Output:
```php
// Trong method index() hoặc search()
echo "<pre>";
print_r($this->data);
echo "</pre>";
exit;
```

---

## 📝 Quick Reference

### Các Page Controllers khác:

| Page | File |
|------|------|
| Host Management | `packages/web/lib/pages/hostmanagementpage.class.php` |
| Task Management | `packages/web/lib/pages/taskmanagementpage.class.php` |
| Group Management | `packages/web/lib/pages/groupmanagementpage.class.php` |
| Storage Management | `packages/web/lib/pages/storagenodemanagementpage.class.php` |
| User Management | `packages/web/lib/pages/usermanagementpage.class.php` |

### MVC Pattern trong FOG:

```
Model (Data):
├── packages/web/lib/fog/image.class.php
├── packages/web/lib/fog/host.class.php
├── packages/web/lib/fog/task.class.php

View (Templates):
├── packages/web/lib/pages/*.class.php (templates property)
├── packages/web/management/css/*.css

Controller (Logic):
├── packages/web/lib/pages/*.class.php (methods)
├── packages/web/management/index.php (router)
```

---

## 🎯 Ví dụ Thực tế: Thay đổi "test - 1" display

File: `packages/web/lib/pages/imagemanagementpage.class.php`

**Hiện tại (dòng ~170):**
```php
'<a href="?node=' . $this->node . '&sub=edit&id=${id}">${name} - ${id}</a>'
```

**Thay đổi thành (Ẩn ID):**
```php
'<a href="?node=' . $this->node . '&sub=edit&id=${id}">${name}</a>'
```

**Hoặc thêm icon:**
```php
'<i class="fa fa-image"></i> <a href="?node=' . $this->node . '&sub=edit&id=${id}">${name}</a>'
```

---

## ✅ Tóm tắt

1. **Main file render UI Image Management:**
   - `packages/web/lib/pages/imagemanagementpage.class.php`

2. **Để xem list images:**
   - Method: `index()` trong file trên

3. **Để sửa table columns:**
   - Sửa `headerData` và `templates` arrays

4. **Để test:**
   - Sửa file → Clear cache → Refresh browser

5. **View trong browser:**
   - `http://[FOG_IP]/fog/management/index.php?node=image`

**File bạn đang thấy trong screenshot chính xác là được render từ:**
`/var/www/html/fog/lib/pages/imagemanagementpage.class.php`
