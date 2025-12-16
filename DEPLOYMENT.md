# Game Module Setup Complete

## Files Ready to Deploy

All module files are in the correct locations:

```
packages/web/lib/fog/
├── game.class.php              ✓ Model
└── gamemanager.class.php       ✓ Manager

packages/web/lib/pages/
└── gamemanagementpage.class.php ✓ Controller (with $node = 'game')
```

## How It Works

FOG automatically loads page classes from `packages/web/lib/pages/` directory.  
The `FOGPageManager` scans for `*.class.php` files and registers classes where `$node` property matches the URL parameter.

Your `gamemanagementpage.class.php` has `public $node = 'game'` which matches `?node=game`.

## Deployment Steps

### 1. Push to Git

```bash
git add packages/web/lib/fog/game*.php
git add packages/web/lib/pages/gamemanagementpage.class.php
git add game_install.sql
git add game_schema.sql
git commit -m "Add Game Management module"
git push origin main
```

### 2. Pull on Server

```bash
ssh root@192.168.150.130

cd /var/www/html/fog
git pull origin main
```

### 3. Install Database

```bash
mysql -u root fog < game_install.sql
```

### 4. Set Permissions

```bash
chown -R www-data:www-data /var/www/html/fog/lib/fog/game*.php
chown -R www-data:www-data /var/www/html/fog/lib/pages/gamemanagementpage.class.php
chmod 644 /var/www/html/fog/lib/fog/game*.php
chmod 644 /var/www/html/fog/lib/pages/gamemanagementpage.class.php
```

### 5. Clear Cache & Restart

```bash
rm -rf /var/www/html/fog/tmp/*
systemctl restart apache2
```

### 6. Access Module

Clear browser cache (Ctrl+Shift+R) and navigate to:
```
http://192.168.150.130/fog/management/?node=game
```

## No Manual Patching Required!

Unlike the old integration instructions, **you don't need to edit** `fogbase.class.php` or `index.php`.  
FOG uses `spl_autoload` and automatically discovers page classes in the `pages/` directory.

## Troubleshooting

If module doesn't appear after deployment:

1. **Check file permissions:**
   ```bash
   ls -la /var/www/html/fog/lib/pages/gamemanagementpage.class.php
   ```

2. **Check Apache error log:**
   ```bash
   tail -f /var/log/apache2/error.log
   ```

3. **Verify database:**
   ```bash
   mysql -u root fog -e "SHOW TABLES LIKE 'games';"
   mysql -u root fog -e "SELECT COUNT(*) FROM games;"
   ```

4. **Test PHP syntax:**
   ```bash
   php -l /var/www/html/fog/lib/pages/gamemanagementpage.class.php
   ```

## Success Criteria

✅ No PHP errors in Apache log  
✅ Database table `games` exists with 5 sample games  
✅ URL `?node=game` loads the Game Management page  
✅ Can add/edit/delete games through the web interface
