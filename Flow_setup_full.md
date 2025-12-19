FOG Project – End-to-End Setup & Debug Guide

Scope: PXE / iPXE / DHCP / TFTP / Apache / PHP / VMware
OS: Ubuntu Server 22.04 LTS (khuyến nghị)
Triết lý: PXE OK ≠ Web OK

1. Kiến trúc tổng thể
[ Client (BIOS/UEFI) ]
        |
        v
      DHCP
        |
        v
      TFTP  ->  undionly.kpxe / ipxe.efi
        |
        v
      iPXE
        |
        v
      HTTP  ->  /fog/service/ipxe/boot.php
        |
        v
      FOG Menu / Task

Nguyên tắc cốt lõi

FOG nhận diện máy bằng MAC, không phải IP

PXE thành công chỉ chứng minh network OK

Web lỗi 503 không liên quan PXE

2. Chuẩn bị OS – Ubuntu Server
2.1 Set IP tĩnh

File: /etc/netplan/00-installer-config.yaml

network:
  version: 2
  ethernets:
    ens33:
      dhcp4: no
      addresses:
        - 192.168.119.10/24
      gateway4: 192.168.119.1
      nameservers:
        addresses:
          - 8.8.8.8

sudo netplan apply
ip a

3. Apache + PHP (NGUYÊN NHÂN LỖI 503)
3.1 Cài PHP 8.1 + PHP-FPM
sudo apt update
sudo apt install -y \
  php8.1 php8.1-fpm php8.1-cli \
  php8.1-mysql php8.1-gd \
  php8.1-curl php8.1-mbstring php8.1-xml

3.2 Kiểm tra PHP-FPM
systemctl status php8.1-fpm
ls -l /run/php/php8.1-fpm.sock


Nếu không có socket → Apache trả về 503 Service Unavailable

4. Cài đặt FOG Project
cd /opt
git clone https://github.com/FOGProject/fogproject.git
cd fogproject/bin
sudo ./installfog.sh

Khi installer hỏi:

DHCP Server → NO (nếu bạn đã có DHCP)

Database → giữ nguyên khi update

5. DHCP – PXE Option (DHCP NGOÀI FOG)

File: /etc/dhcp/dhcpd.conf

subnet 192.168.119.0 netmask 255.255.255.0 {
  range 192.168.119.100 192.168.119.200;
  option routers 192.168.119.1;
  option domain-name-servers 8.8.8.8;

  next-server 192.168.119.10;
  filename "undionly.kpxe";
}

sudo systemctl restart isc-dhcp-server


❌ KHÔNG trỏ trực tiếp boot.php trong DHCP

6. TFTP & iPXE
6.1 TFTP root
ls /tftpboot


Phải có:

undionly.kpxe (BIOS)

ipxe.efi (UEFI)

default.ipxe (nếu dùng)

7. default.ipxe (NGUYÊN NHÂN LOOP IP CŨ)

File: /tftpboot/default.ipxe

#!ipxe
dhcp
set fog-ip 192.168.119.10
chain http://${fog-ip}/fog/service/ipxe/boot.php || shell

Vì sao bắt buộc || shell

Không có shell → iPXE đứng hình

VMware không bắt được phím

8. VMware Client (CỰC KỲ QUAN TRỌNG)
8.1 Network Adapter

Bridged hoặc Custom đúng VLAN

MAC cố định

8.2 Khi test PXE

❌ Restart

✅ Power Off → Power On

VMware cache PXE rất mạnh

9. Không Quick Register vẫn tạo Host được
9.1 Tạo host từ Web
FOG Web → Hosts → Create New Host


Chỉ cần:

Hostname

Primary MAC

IP KHÔNG dùng để nhận diện host

10. Kiểm tra Host đã OK hay chưa
10.1 Dấu hiệu trên Web

Host xuất hiện trong List All Hosts

Last Seen cập nhật sau PXE

10.2 Log trên server
tail -n 50 /opt/fog/log/fog.log


Tìm:

Found Host by MAC: xx:xx:xx:xx:xx:xx

11. Workflow Image chuẩn
1. Cài OS trên máy mẫu
2. Image Management → Create Image
3. Assign Image cho Host
4. Capture
5. Deploy cho máy khác


Không bật Auto Deploy khi chưa test xong

12. Các lỗi THỰC TẾ & nguyên nhân
Triệu chứng	Nguyên nhân
PXE trỏ IP cũ	default.ipxe hard-code
PXE đứng hình	thiếu shell
Web 503	thiếu PHP-FPM
PXE OK, Web chết	Apache chạy, PHP chết
PXE hỏi register	MAC không match
13. Checklist debug nhanh (10 phút)
[ ] Client lấy IP đúng subnet
[ ] DHCP next-server đúng
[ ] TFTP trả undionly.kpxe
[ ] default.ipxe không hard-code IP cũ
[ ] Apache + PHP-FPM đều chạy
[ ] Host có Last Seen