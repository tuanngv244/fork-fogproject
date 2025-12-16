# FOG Client Files Installation

The FOG client files are missing from the container. Follow these steps to add them:

## Download the files

Download these files from the FOG Project GitHub:
- SmartInstaller.exe: https://github.com/FOGProject/fog-client/releases/latest/download/SmartInstaller.exe
- FOGService.msi: https://github.com/FOGProject/fog-client/releases/latest/download/FOGService.msi
- FogPrep.zip: https://github.com/FOGProject/fogproject/releases/latest/download/FogPrep.zip

## Copy to container

Place the downloaded files in `d:\fogproject\packages\web\client\` on your host machine.

The files will automatically be available in the container at `/var/www/html/fog/client/`.

## Alternative: Manual copy to container

```powershell
# Copy SmartInstaller.exe to container
docker cp SmartInstaller.exe fog-server:/var/www/html/fog/client/

# Copy FOGService.msi to container  
docker cp FOGService.msi fog-server:/var/www/html/fog/client/

# Copy FogPrep.zip to container
docker cp FogPrep.zip fog-server:/var/www/html/fog/client/
```

After copying the files, refresh the download page.
