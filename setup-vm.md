🔹 BƯỚC 2 – Tạo máy ảo Linux

ISO khuyến nghị:
👉 Ubuntu Server 22.04 LTS

Cấu hình VM:

CPU: 4 cores
RAM: 8GB
Disk: 200GB (Thin Provision ok)
Network: BRIDGED (BẮT BUỘC)


⚠️ KHÔNG dùng NAT → PXE sẽ không chạy

🔹 BƯỚC 3 – Cài Ubuntu Server

Chọn OpenSSH Server

Network để DHCP (auto)

Không cần GUI

Sau khi vào được terminal:

ip a


👉 ghi lại IP của VM (vd: 192.168.1.50)

🔹 BƯỚC 4 – Cài FOG Server
sudo -i
apt update && apt upgrade -y
apt install -y git
git clone https://github.com/FOGProject/fogproject.git
cd fogproject/bin
./installfog.sh


Khi hỏi:

OS: Ubuntu
Installation type: Normal Server
DHCP: 
  - YES nếu bạn chưa có DHCP khác
  - NO nếu router đã cấp DHCP

🔹 BƯỚC 5 – Truy cập Web quản trị

Mở trình duyệt trên Windows:

http://IP_FOG_SERVER/fog


➡ Bấm Install / Update Database

⚠️ CẤU HÌNH MẠNG RẤT QUAN TRỌNG (ĐỌC KỸ)
Nếu LAN đã có DHCP (router/modem)

→ KHÔNG bật DHCP trong FOG

Cần cấu hình router:

Option 66 → IP FOG Server
Option 67 → undionly.kpxe

Nếu không có DHCP

→ Bật DHCP trong FOG