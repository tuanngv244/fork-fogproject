# FOG REST API Documentation

## Base URL
```
http://your-fog-server/fog/service/
```

---

## 📦 IMAGE API (`/image.php`)

### List All Images
```http
GET /fog/service/image.php
GET /fog/service/image.php?page=1&limit=50
GET /fog/service/image.php?isEnabled=1
GET /fog/service/image.php?storagegroup=1
```

**Response:**
```json
{
  "success": true,
  "message": "Images retrieved successfully",
  "data": {
    "images": [
      {
        "id": 1,
        "name": "Windows 10 Pro",
        "description": "Windows 10 Professional x64",
        "path": "win10pro",
        "osID": 5,
        "osName": "Windows 7",
        "imageTypeID": 1,
        "imageType": "Single Partition",
        "imagePartitionTypeID": 1,
        "partitionType": "Everything",
        "storageGroupID": 1,
        "storageGroupName": "default",
        "compress": 6,
        "isEnabled": true,
        "toReplicate": true,
        "protected": false,
        "format": 0,
        "formatName": "Partclone",
        "size": 15728640000,
        "sizeFormatted": "14.65 GiB",
        "deployed": "2025-12-18 10:30:00",
        "createdTime": "2025-12-01 09:00:00",
        "createdBy": "admin",
        "hostCount": 5
      }
    ],
    "pagination": {
      "total": 10,
      "page": 1,
      "limit": 50,
      "pages": 1
    }
  }
}
```

### Get Single Image
```http
GET /fog/service/image.php?id=1
```

**Response:** Includes `storageNodes` and `hosts` arrays

### Create Image
```http
POST /fog/service/image.php
Content-Type: application/json

{
  "name": "Ubuntu 22.04 LTS",
  "description": "Ubuntu Desktop 22.04",
  "path": "ubuntu2204",
  "osID": 9,
  "imageTypeID": 1,
  "imagePartitionTypeID": 1,
  "storagegroup": 1,
  "compress": 6,
  "isEnabled": true,
  "format": 0,
  "toReplicate": true,
  "protected": false
}
```

**Required Fields:**
- `name` - Image name (must be unique)
- `path` - Storage path (must be unique, not reserved)
- `storagegroup` - Storage group ID

**Optional Fields:**
- `description` - Description
- `osID` - OS ID (default: 5 = Windows 7)
- `imageTypeID` - Image type (default: 1 = Single Partition)
- `imagePartitionTypeID` - Partition type (default: 1 = Everything)
- `compress` - Compression level 0-22 (default: 6)
- `isEnabled` - Enable image (default: true)
- `format` - 0=Partclone, 1=Partimage (default: 0)
- `toReplicate` - Replicate to storage nodes (default: true)
- `protected` - Protect from capture (default: false)

### Update Image
```http
PUT /fog/service/image.php?id=1
Content-Type: application/json

{
  "name": "Windows 10 Pro v2",
  "description": "Updated description",
  "isEnabled": false
}
```

### Delete Image
```http
DELETE /fog/service/image.php?id=1
```

**Validation:**
- Cannot delete if protected
- Cannot delete if hosts are using it
- Cannot delete if active tasks exist

---

## 🖥️ HOST API (`/host.php`)

### List All Hosts
```http
GET /fog/service/host.php
GET /fog/service/host.php?page=1&limit=50
GET /fog/service/host.php?imageID=5
GET /fog/service/host.php?pending=1
```

### Get Single Host
```http
GET /fog/service/host.php?id=123
GET /fog/service/host.php?mac=00:11:22:33:44:55
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "PC-LAB-01",
    "description": "Lab computer 1",
    "mac": "00:11:22:33:44:55",
    "imageID": 5,
    "imageName": "Windows 10 Pro",
    "pending": false,
    "deployed": "2025-12-18 10:30:00",
    "currentTask": {
      "id": 456,
      "name": "Deploy Task - PC-LAB-01",
      "typeID": 1,
      "type": "Deploy",
      "stateID": 1,
      "state": "Queued",
      "percent": 0
    }
  }
}
```

### Create Host
```http
POST /fog/service/host.php
Content-Type: application/json

{
  "name": "PC-LAB-02",
  "mac": "00:11:22:33:44:66",
  "description": "Lab computer 2",
  "imageID": 5,
  "pending": ""
}
```

### Update Host
```http
PUT /fog/service/host.php?id=123
Content-Type: application/json

{
  "imageID": 6,
  "description": "Updated description"
}
```

### Delete Host
```http
DELETE /fog/service/host.php?id=123
```

---

## 📋 TASK API (`/task.php`)

### List Tasks
```http
GET /fog/service/task.php
GET /fog/service/task.php?hostID=123
GET /fog/service/task.php?imageID=5
GET /fog/service/task.php?stateID=1
GET /fog/service/task.php?typeID=1
```

**Task Types:**
- `1` - Deploy (Download)
- `2` - Capture (Upload)
- `8` - Multicast
- `13` - Deploy - Snapin

**Task States:**
- `1` - Queued
- `2` - In Progress
- `3` - Complete
- `4` - Cancelled
- `5` - Failed

### Get Single Task
```http
GET /fog/service/task.php?id=456
```

### Create Deploy Task
```http
POST /fog/service/task.php
Content-Type: application/json

{
  "hostID": 123,
  "taskType": 1,
  "name": "Deploy Windows 10",
  "shutdown": true,
  "debug": false,
  "wol": false,
  "username": "API"
}
```

**Required:**
- `hostID` - Host ID
- `taskType` - 1=Deploy, 2=Capture, 8=Multicast

**Optional:**
- `name` - Task name (auto-generated if omitted)
- `shutdown` - Shutdown after task (default: false)
- `debug` - Debug mode (default: false)
- `wol` - Wake-on-LAN (default: false)
- `username` - Creator username (default: "API")

**Validation:**
- Host must exist and not be pending
- Host must have image assigned (for imaging tasks)
- Image must be enabled
- No active imaging task on host
- Storage node must be available

### Create Capture Task
```http
POST /fog/service/task.php
Content-Type: application/json

{
  "hostID": 123,
  "taskType": 2,
  "shutdown": true
}
```

**Additional validation for capture:**
- Image cannot be protected

### Cancel Task
```http
DELETE /fog/service/task.php?id=456
```

---

## 🎮 GAME API (`/game.php`)

### List All Games
```http
GET /fog/service/game.php
```

### Get Single Game
```http
GET /fog/service/game.php?id=1
```

### Create Game
```http
POST /fog/service/game.php
Content-Type: application/json

{
  "name": "Counter-Strike 2",
  "downloadPath": "http://cdn.example.com/cs2.zip",
  "description": "FPS Game",
  "icon": "gamepad"
}
```

### Update Game
```http
PUT /fog/service/game.php?id=1
Content-Type: application/json

{
  "name": "CS2 Updated",
  "downloadPath": "http://cdn2.example.com/cs2.zip"
}
```

### Delete Game
```http
DELETE /fog/service/game.php?id=1
```

---

## 📊 WORKFLOW EXAMPLES

### Complete Image Deployment Workflow

**1. Create Image:**
```bash
curl -X POST http://fog/fog/service/image.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Windows 11 Pro",
    "path": "win11pro",
    "storagegroup": 1,
    "osID": 9,
    "compress": 6
  }'
```

**2. Create Host:**
```bash
curl -X POST http://fog/fog/service/host.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "PC-01",
    "mac": "00:11:22:33:44:55",
    "imageID": 5
  }'
```

**3. Create Deploy Task:**
```bash
curl -X POST http://fog/fog/service/task.php \
  -H "Content-Type: application/json" \
  -d '{
    "hostID": 123,
    "taskType": 1,
    "shutdown": true,
    "wol": true
  }'
```

**4. Monitor Task:**
```bash
curl http://fog/fog/service/task.php?id=456
```

**5. Check Host Status:**
```bash
curl http://fog/fog/service/host.php?id=123
```

### Capture Workflow

**1. Create Capture Task:**
```bash
curl -X POST http://fog/fog/service/task.php \
  -H "Content-Type: application/json" \
  -d '{
    "hostID": 123,
    "taskType": 2,
    "shutdown": true
  }'
```

**2. Boot Host via PXE** - FOG will automatically:
   - Detect task
   - Load capture kernel
   - Upload image to master storage node
   - Update image metadata
   - Replicate to other nodes (if enabled)

---

## 🔒 ERROR HANDLING

All APIs return consistent error format:

```json
{
  "success": false,
  "message": "Error description",
  "data": null
}
```

**HTTP Status Codes:**
- `200` - OK
- `201` - Created
- `400` - Bad Request (validation error)
- `403` - Forbidden (protected resource)
- `404` - Not Found
- `405` - Method Not Allowed
- `409` - Conflict (duplicate/in use)
- `500` - Internal Server Error

---

## 🔐 SECURITY NOTES

**Current Implementation:**
- No authentication required
- Direct database access
- For trusted network use only

**Recommended Enhancements:**
- Add API key authentication
- Implement rate limiting
- Add CORS headers for web apps
- Use HTTPS in production
- Add request logging
- Implement user permissions

---

## 💡 INTEGRATION EXAMPLES

### Python
```python
import requests

class FOGClient:
    def __init__(self, base_url):
        self.base_url = base_url
        
    def deploy_image(self, host_id, shutdown=True):
        return requests.post(
            f"{self.base_url}/task.php",
            json={
                "hostID": host_id,
                "taskType": 1,
                "shutdown": shutdown
            }
        ).json()

fog = FOGClient("http://192.168.1.10/fog/service")
result = fog.deploy_image(123)
print(result)
```

### PowerShell
```powershell
function New-FOGDeployTask {
    param(
        [int]$HostID,
        [bool]$Shutdown = $true
    )
    
    $body = @{
        hostID = $HostID
        taskType = 1
        shutdown = $Shutdown
    } | ConvertTo-Json
    
    Invoke-RestMethod -Uri "http://fog/fog/service/task.php" `
                      -Method Post `
                      -Body $body `
                      -ContentType "application/json"
}
```

### JavaScript/Node.js
```javascript
const axios = require('axios');

class FOGClient {
    constructor(baseURL) {
        this.api = axios.create({ baseURL });
    }
    
    async deployImage(hostID, options = {}) {
        return this.api.post('/task.php', {
            hostID,
            taskType: 1,
            ...options
        });
    }
}

const fog = new FOGClient('http://192.168.1.10/fog/service');
fog.deployImage(123, { shutdown: true, wol: true });
```
