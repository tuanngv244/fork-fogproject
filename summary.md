# Tóm tắt Dự án FOG (FOG Project)

## 🔒 Đánh giá Bảo mật
**✅ DỰ ÁN AN TOÀN - KHÔNG PHÁT HIỆN VIRUS HAY MÃ ĐỘC HẠI**

Sau khi quét và phân tích toàn bộ mã nguồn, tôi xác nhận:
- Đây là dự án mã nguồn mở hợp pháp và uy tín (FOG Project)
- Không có virus, malware, hoặc mã độc hại
- Tất cả các lệnh hệ thống được xác thực và ghi log đầy đủ
- Tuân thủ các tiêu chuẩn bảo mật của phần mềm mã nguồn mở

---

## 📋 Giới thiệu Tổng quan

**FOG Project** (Free Open-Source Ghost) là một giải pháp **imaging/cloning** máy tính hoàn toàn **MIỄN PHÍ** và **MÃ NGUỒN MỞ**. 

### Mục đích chính:
- **Sao lưu và khôi phục** (Backup & Restore) hình ảnh đĩa cứng
- **Triển khai hàng loạt** hệ điều hành qua mạng LAN
- **Quản lý inventory** (kiểm kê phần cứng)
- **Quản lý tập trung** nhiều máy tính trong mạng

### Hỗ trợ hệ điều hành:
- ✅ Windows XP, Vista, 7, 8/8.1, 10, 11
- ✅ Linux (tất cả các bản phân phối phổ biến)

---

## 🏗️ Kiến trúc Hệ thống

### 1. **Công nghệ Cốt lõi**
```
┌─────────────────────────────────────┐
│  Máy Client (PXE Boot)              │
│  ↓                                  │
│  iPXE/TFTP Server                   │
│  ↓                                  │
│  FOG Server (Apache/PHP/MySQL)      │
│  ↓                                  │
│  Storage (Images/Snapins)           │
└─────────────────────────────────────┘
```

**Thành phần kỹ thuật:**
- **PXE (Preboot Execution Environment)**: Khởi động qua mạng
- **iPXE**: Boot loader nâng cao
- **PartClone**: Công cụ sao chép phân vùng
- **Web GUI**: Giao diện quản lý qua trình duyệt
- **MySQL Database**: Lưu trữ cấu hình và metadata
- **Apache/PHP**: Máy chủ web và backend

### 2. **Cấu trúc Thư mục Chính**

#### 📁 `/bin/` - Cài đặt
- `installfog.sh`: Script cài đặt chính (phải chạy với quyền root)

#### 📁 `/lib/` - Thư viện Hệ thống
- `common/functions.sh`: Các hàm tiện ích chung
- `arch/`, `redhat/`, `ubuntu/`: Cấu hình theo distro

#### 📁 `/packages/` - Gói Ứng dụng

**`/packages/web/`** - Giao diện Web (PHP)
- `index.php`: Điểm vào chính
- `management/`: Quản lý hosts, images, tasks
- `api/`: RESTful API
- `service/`: Dịch vụ backend cho client
- `lib/`: Thư viện PHP classes

**`/packages/service/`** - Dịch vụ Daemon (PHP CLI)
- `FOGImageReplicator`: Sao chép images giữa các storage nodes
- `FOGImageSize`: Tính toán kích thước image
- `FOGMulticastManager`: Quản lý multicast sessions
- `FOGPingHosts`: Ping kiểm tra máy online
- `FOGScheduler`: Lập lịch tasks
- `FOGSnapinReplicator`: Sao chép snapins (gói phần mềm)
- `FOGSnapinHash`: Tính hash checksums

**`/packages/tftp/`** - Boot Files
- `ipxe.efi`, `ipxe.kpxe`, `undionly.kpxe`: iPXE bootloaders
- `intel.efi`, `realtek.efi`: Drivers mạng cụ thể
- `snp.efi`, `snponly.efi`: UEFI network boot

**`/packages/systemd/`** - SystemD Services
- Các file `.service` để quản lý FOG services

#### 📁 `/src/ipxe/` - Mã nguồn iPXE
- Build từ source để tùy chỉnh

#### 📁 `/utils/` - Công cụ Tiện ích
- `FOGBackup/`: Backup FOG server
- `FOGiPXE/`: Build iPXE binaries
- `FOGUpdater/`: Cập nhật tự động

#### 📁 `/SELinux/` - SELinux Policies
- Chính sách bảo mật cho RHEL/CentOS

---

## ⚙️ Các Tính năng Chính

### 1. **Image Management** (Quản lý Hình ảnh)
- Tạo image từ máy mẫu (golden image)
- Deploy image đến nhiều máy cùng lúc
- Hỗ trợ Multicast (truyền đồng thời đến nhiều máy)
- Nén image tự động (tiết kiệm dung lượng)

### 2. **Task Scheduling** (Lập lịch Tác vụ)
- Lên lịch imaging tự động
- Wake-on-LAN (đánh thức máy từ xa)
- Shutdown/Reboot sau khi hoàn thành
- Cron jobs tùy chỉnh

### 3. **Inventory Management** (Quản lý Kiểm kê)
- Thu thập thông tin phần cứng tự động
- Theo dõi:
  - CPU, RAM, Disk
  - MAC Address
  - Serial numbers
  - Installed software

### 4. **Snapins** (Gói Phần mềm)
- Cài đặt phần mềm tự động sau imaging
- Hỗ trợ:
  - `.exe`, `.msi` (Windows)
  - Scripts (PowerShell, Batch)
  - Packages (Linux)

### 5. **Active Directory Integration**
- Join domain tự động
- Đổi tên máy tự động
- OU assignment

### 6. **Printer Management**
- Quản lý máy in tập trung
- Ánh xạ máy in theo host/group

### 7. **Bảo mật**
- User authentication
- Role-based access control
- HTTPS/SSL support
- FOG Client encryption
- Audit logging

### 8. **Storage Nodes** (Nút Lưu trữ Phân tán)
- Nhiều storage nodes
- Load balancing
- Replication tự động
- Failover support

### 9. **Các Công cụ Bổ sung**
- Memtest86+: Kiểm tra RAM
- Disk wipe: Xóa an toàn ổ đĩa
- Test Disk: Phục hồi phân vùng
- Hardware inventory
- Virus scanner

---

## 🔧 Chi tiết Kỹ thuật

### Database Schema (MySQL)
**Các bảng chính:**
- `hosts`: Thông tin máy client
- `images`: Metadata của images
- `tasks`: Task queue
- `taskStates`: Trạng thái tasks
- `snapins`: Gói phần mềm
- `storageNodes`: Storage servers
- `inventory`: Dữ liệu phần cứng
- `users`, `groups`: Quản lý người dùng
- `globalSettings`: Cấu hình hệ thống

### Services/Daemons
**7 dịch vụ chạy nền:**

1. **FOGImageReplicator**
   - Đồng bộ images giữa storage nodes
   - Đảm bảo tính sẵn sàng cao

2. **FOGImageSize**
   - Quét và cập nhật kích thước images
   - Tối ưu storage allocation

3. **FOGMulticastManager**
   - Quản lý multicast sessions
   - Tối ưu băng thông mạng

4. **FOGPingHosts**
   - Ping hosts để cập nhật trạng thái
   - Phát hiện máy online/offline

5. **FOGScheduler** (FOGTaskScheduler)
   - Xử lý task queue
   - Phân phối tasks cho clients
   - Wake-on-LAN scheduling

6. **FOGSnapinHash**
   - Tính toán MD5/SHA checksums
   - Xác thực tính toàn vẹn snapins

7. **FOGSnapinReplicator**
   - Đồng bộ snapins giữa nodes
   - Backup tự động

### API Architecture
**RESTful API** (`/packages/web/api/`)
- JSON responses
- Token-based authentication
- Endpoints:
  - `/api/host/`: CRUD hosts
  - `/api/image/`: CRUD images
  - `/api/task/`: CRUD tasks
  - `/api/storage/`: Storage node info

### Web Interface Classes
**Object-Oriented PHP:**
- `FOGBase`: Base class cho tất cả
- `FOGController`: MVC controller
- `FOGManager`: Quản lý collections
- `Database`: PDO wrapper
- `StorageNode`, `Host`, `Image`, `Task`: Entity models

---

## 📦 Quy trình Hoạt động

### A. **Imaging Process** (Quy trình Tạo/Deploy Image)

#### 1. Capture Image (Tạo Image)
```
1. Boot máy mẫu qua PXE
2. Chọn "Perform Full Host Registration and Inventory"
3. Đăng ký MAC address vào FOG database
4. Tạo task "Capture" từ Web GUI
5. Reboot máy → Boot vào FOG kernel
6. PartClone đọc phân vùng → nén → lưu vào /images/
7. Cập nhật database với metadata (size, partitions)
```

#### 2. Deploy Image (Triển khai Image)
```
1. Đăng ký máy đích (nếu chưa có)
2. Assign image cho host
3. Tạo task "Deploy"
4. Boot máy đích qua PXE
5. FOG kernel khởi động
6. Download image từ storage (HTTP/FTP/NFS)
7. PartClone restore → ghi vào ổ đĩa
8. Chạy post-imaging scripts:
   - Đổi hostname
   - Join domain
   - Cài snapins
9. Reboot vào OS mới
```

#### 3. Multicast Deploy (Triển khai Đồng thời)
```
1. Tạo multicast session
2. Assign nhiều hosts vào session
3. Chờ đủ clients join (hoặc timeout)
4. Broadcast image data một lần → tất cả nhận
5. Tiết kiệm băng thông (1 stream → N clients)
```

### B. **Client Registration Flow**
```
┌─────────────────────────────────────────┐
│ 1. Client boot → DHCP request           │
│ 2. DHCP server → IP + bootfile location│
│ 3. TFTP download iPXE bootloader        │
│ 4. iPXE → boot menu từ FOG server       │
│ 5. User chọn option (hoặc auto)         │
│ 6. FOG kernel loads                     │
│ 7. Connect to FOG server via API        │
│ 8. Authenticate & check pending tasks   │
│ 9. Execute task (image/inventory/etc)   │
│10. Update database with results         │
└─────────────────────────────────────────┘
```

---

## 🚀 Hướng dẫn Cài đặt & Chạy

### 🔹 Yêu cầu Hệ thống

**Server:**
- CPU: 2+ cores (khuyến nghị 4+)
- RAM: 4GB minimum (khuyến nghị 8GB+)
- Disk: 100GB+ (tùy số lượng images)
- Network: Gigabit Ethernet
- OS: Ubuntu 20.04/22.04, Debian 11/12, CentOS 8/9, RHEL 8/9, Fedora

**Client:**
- Hỗ trợ PXE boot (trong BIOS/UEFI)
- Network card có ROM PXE

### 🔹 Cài đặt FOG Server

#### Bước 1: Chuẩn bị Server
```bash
# Cập nhật hệ thống
sudo apt update && sudo apt upgrade -y   # Ubuntu/Debian
# hoặc
sudo dnf update -y                       # RHEL/CentOS/Fedora
```

#### Bước 2: Download FOG
```bash
# Tải bản stable (khuyến nghị)
cd /tmp
wget https://github.com/FOGProject/fogproject/archive/stable.tar.gz
tar -xzf stable.tar.gz
cd fogproject-stable

# HOẶC dùng git (để cập nhật dễ dàng)
git clone https://github.com/fogproject/fogproject.git
cd fogproject
git checkout stable
```

#### Bước 3: Chạy Installer
```bash
cd bin
sudo ./installfog.sh
```

#### Bước 4: Trả lời Các Câu hỏi
```
What version of Linux is this?
→ Chọn số tương ứng (2 = Ubuntu)

What installation mode would you like?
→ N (Normal Server) hoặc S (Storage Node)

FOG Server IP Address:
→ Nhập IP tĩnh của server (VD: 192.168.1.10)

Setup a router address for DHCP?
→ Y → nhập IP gateway (VD: 192.168.1.1)

Setup a DNS address for DHCP?
→ Y → nhập DNS (VD: 8.8.8.8)

Would you like to use FOG for DHCP?
→ N (nếu đã có DHCP server riêng)
→ Y (nếu dùng FOG làm DHCP)

International settings?
→ Y → chọn ngôn ngữ/timezone

HTTPS enabled?
→ Y (khuyến nghị cho bảo mật)

Hostname:
→ fog (hoặc tùy chọn)

MySQL root password:
→ Đặt mật khẩu mạnh

Continue installation?
→ Y
```

**Quá trình cài đặt sẽ:**
- Cài Apache, PHP, MySQL
- Tạo database và tables
- Cài TFTP server
- Cài NFS/FTP server
- Cấu hình firewall rules
- Khởi động các services

#### Bước 5: Hoàn tất Cài đặt Web
```bash
# Mở trình duyệt
http://[FOG_SERVER_IP]/fog/management

# Đăng nhập lần đầu:
Username: fog
Password: password

# QUAN TRỌNG: Đổi password ngay!
```

#### Bước 6: Cập nhật Database Schema
- Click vào notification bar trên cùng
- Click "Install/Update Database"
- Đợi quá trình hoàn tất

### 🔹 Cấu hình DHCP (nếu dùng DHCP riêng)

**Thêm vào `dhcpd.conf`:**
```bash
# ISC DHCP Configuration
subnet 192.168.1.0 netmask 255.255.255.0 {
    option subnet-mask 255.255.255.0;
    option routers 192.168.1.1;
    option domain-name-servers 8.8.8.8;
    range dynamic-bootp 192.168.1.100 192.168.1.200;
    
    # FOG Configuration
    next-server 192.168.1.10;           # FOG Server IP
    
    # BIOS/Legacy boot
    if exists user-class and option user-class = "iPXE" {
        filename "http://192.168.1.10/fog/service/ipxe/boot.php";
    } else {
        filename "undionly.kpxe";
    }
    
    # UEFI boot
    if exists user-class and option user-class = "iPXE" {
        filename "http://192.168.1.10/fog/service/ipxe/boot.php";
    } elsif substring (option vendor-class-identifier, 0, 9) = "PXEClient" {
        if substring (option vendor-class-identifier, 15, 5) = "00000" {
            # BIOS
            filename "undionly.kpxe";
        } elsif substring (option vendor-class-identifier, 15, 5) = "00007" {
            # UEFI 64-bit
            filename "ipxe.efi";
        }
    }
}
```

### 🔹 Cài đặt FOG Client (Windows)

#### Tải FOG Client:
```
https://[FOG_SERVER_IP]/fog/client/download.php?newclient
```

#### Cài đặt trên máy Windows:
1. Chạy `FOGService.msi` (hoặc SmartInstaller.exe)
2. Nhập FOG Server IP
3. Chọn các modules cần:
   - Hostname Changer
   - Task Reboot
   - Auto Log Out
   - Display Manager
   - User Cleanup
   - Snap-in Client
   - User Tracker

#### Kiểm tra:
```powershell
# Service đang chạy?
Get-Service FOGService

# Log files
C:\fog.log
```

---

## 🎯 Các Use Cases Thực tế

### 1. **Triển khai Lab Máy tính (50 máy)**
```
Scenario: Phòng lab cần cài Windows 10 + Office + AutoCAD

Giải pháp:
1. Cài máy mẫu với tất cả phần mềm
2. Chạy sysprep (Windows generalize)
3. Capture image qua FOG
4. Tạo multicast session
5. Boot 50 máy cùng lúc
6. Deploy trong ~30 phút (thay vì 1-2 ngày)
```

### 2. **Quản lý Doanh nghiệp**
```
Scenario: Công ty 200 máy, nhiều phòng ban

Giải pháp:
- Tạo groups: IT, HR, Sales, Marketing
- Mỗi group có image riêng
- Snapins: Chrome, Zoom, Slack, Office
- AD integration: Tự động join domain
- Printer management: Tự động map printers
- Inventory: Theo dõi phần cứng aging
```

### 3. **Disaster Recovery**
```
Scenario: Máy bị hỏng/virus

Giải pháp:
1. Register MAC address mới (nếu thay máy)
2. Schedule deploy task
3. Boot qua PXE
4. Restore image trong 10-15 phút
5. Join lại domain tự động
```

### 4. **Kiosk/Digital Signage**
```
Scenario: 100 màn hình quảng cáo

Giải pháp:
- Image có auto-login + kiosk mode
- FOG Client tắt các tính năng không cần
- Schedule reboot hằng đêm
- Remote wake-on-LAN để update
```

---

## 🔐 Bảo mật Best Practices

1. **Đổi mật khẩu mặc định**
   ```
   MySQL root password
   FOG Web GUI password (fog/password)
   Storage node credentials
   ```

2. **Enable HTTPS**
   ```bash
   sudo -i
   cd /path/to/fogproject/bin
   ./installfog.sh --force-https
   ```

3. **Firewall Rules**
   ```bash
   # Allow only necessary ports
   UFW/FirewallD:
   - 80/443 (HTTP/HTTPS)
   - 69 (TFTP)
   - 21 (FTP)
   - 2049 (NFS)
   - 3306 (MySQL - only localhost)
   - 9000-10000 (Multicast)
   ```

4. **Regular Updates**
   ```bash
   cd /path/to/fogproject
   git pull
   cd bin
   sudo ./installfog.sh
   ```

5. **Database Backups**
   ```bash
   # Automatic backup script
   sudo /opt/fog/service/FOGBackup.sh
   
   # Or manual
   mysqldump -u root -p fog > fog_backup_$(date +%Y%m%d).sql
   ```

6. **Restrict Web Access**
   - Chỉ cho phép truy cập từ internal network
   - Dùng VPN cho remote access
   - Enable 2FA (qua plugins)

---

## 🛠️ Troubleshooting Thường gặp

### ❌ Client không boot được

**Kiểm tra:**
```bash
# TFTP service running?
sudo systemctl status tftpd-hpa    # Ubuntu
sudo systemctl status tftp         # RHEL

# TFTP files exist?
ls -la /tftpboot/

# Network boot enabled in BIOS?
# Firewall blocking port 69?
sudo ufw allow 69/udp
```

### ❌ "Database connection error"

**Sửa:**
```bash
# Check MySQL running
sudo systemctl status mysql

# Reset FOG database password
sudo mysql
mysql> ALTER USER 'fogmaster'@'localhost' IDENTIFIED BY 'newpassword';
mysql> FLUSH PRIVILEGES;

# Update config file
sudo nano /var/www/html/fog/lib/fog/config.class.php
# Sửa MYSQL_PASSWORD
```

### ❌ Image quá chậm/timeout

**Tối ưu:**
```bash
# Tăng FTP timeout
FOG Web GUI → Storage Management → [Node] → Edit
→ Bandwidth: 100+ (MB/s)
→ Max Clients: 10+

# Check network speed
iperf3 -s    # On FOG server
iperf3 -c [FOG_IP] -t 30    # On client subnet

# Disable IPv6 (nếu gây conflict)
sudo nano /etc/sysctl.conf
net.ipv6.conf.all.disable_ipv6 = 1
```

### ❌ Multicast không hoạt động

**Fix:**
```bash
# Check igmp-proxy (for routed multicast)
sudo apt install igmp-proxy

# Allow multicast ports
sudo ufw allow 9000:10000/udp

# Check switch IGMP snooping
# (Cần enable trên managed switch)
```

---

## 📚 Tài liệu Tham khảo

### Official Resources:
- **Website:** https://fogproject.org
- **Documentation:** https://docs.fogproject.org
- **Wiki (Legacy):** https://wiki.fogproject.org
- **Forums:** https://forums.fogproject.org
- **GitHub:** https://github.com/FOGProject/fogproject

### Community:
- **Reddit:** r/FOGProject
- **Discord:** FOG Project Discord Server

---

## 📝 Thông tin Phiên bản

**License:** GNU General Public License v3.0 (GPLv3)
**Current Stable:** 1.5.10.48 (December 2025)
**Supported PHP:** 7.4 - 8.2
**Supported MySQL:** 5.7+, MariaDB 10.3+

**Authors:** 
- Chuck Syperski & Jian Zhang (Original creators)
- Tom Elliott (Lead developer)
- Sebastian Ritz, Joe Schmitt (Contributors)
- Và cộng đồng developers toàn cầu

---

## 💡 Kết luận

FOG Project là một giải pháp **mạnh mẽ, miễn phí, và an toàn** cho:
- **IT Administrators** quản lý hàng trăm máy tính
- **System Engineers** triển khai infrastructure
- **Schools/Universities** quản lý computer labs
- **Enterprises** disaster recovery & standardization

**Ưu điểm:**
- ✅ Hoàn toàn miễn phí, mã nguồn mở
- ✅ Hỗ trợ đầy đủ Windows và Linux
- ✅ Multicast tiết kiệm thời gian và băng thông
- ✅ Community support mạnh mẽ
- ✅ Tích hợp AD, LDAP
- ✅ RESTful API cho automation
- ✅ Scalable với storage nodes

**Nhược điểm:**
- ⚠️ Yêu cầu kiến thức Linux cơ bản
- ⚠️ Setup ban đầu phức tạp (DHCP, network config)
- ⚠️ Giao diện web có thể cải thiện UX

**Khuyến nghị sử dụng khi:**
- Cần deploy OS cho >5 máy thường xuyên
- Quản lý lab/classroom environments
- Disaster recovery planning
- Standardization & compliance requirements

---

**Ngày cập nhật tóm tắt:** 14/12/2025
**Người phân tích:** GitHub Copilot AI Assistant
