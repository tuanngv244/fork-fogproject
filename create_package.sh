#!/bin/bash
#
# Create Game Module Package for FOG Project
# Compatible with Ubuntu/Debian/Linux
#

set -e

echo "========================================="
echo "  FOG Game Module Package Creator"
echo "========================================="
echo ""

# Package directory
PACKAGE_DIR="game_module_package"

# Remove old package if exists
if [ -d "$PACKAGE_DIR" ]; then
    echo "[1/5] Removing old package..."
    rm -rf "$PACKAGE_DIR"
fi

# Create package directory
echo "[2/5] Creating package directory..."
mkdir -p "$PACKAGE_DIR"

# Copy module files
echo "[3/5] Copying module files..."
cp game.class.php "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: game.class.php not found"
cp gamemanager.class.php "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: gamemanager.class.php not found"
cp gamemanagementpage.class.php "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: gamemanagementpage.class.php not found"

# Copy SQL files
echo "[4/5] Copying SQL files..."
cp game_schema.sql "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: game_schema.sql not found"
cp game_install.sql "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: game_install.sql not found"

# Copy integration files
echo "[5/5] Copying integration files..."
cp apply_patches.sh "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: apply_patches.sh not found"
cp PATCH_1_fogbase.txt "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: PATCH_1_fogbase.txt not found"
cp PATCH_2_index.txt "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: PATCH_2_index.txt not found"
cp INTEGRATION_FIX.md "$PACKAGE_DIR/" 2>/dev/null || echo "  Warning: INTEGRATION_FIX.md not found"

# Make scripts executable
chmod +x "$PACKAGE_DIR/apply_patches.sh" 2>/dev/null || true

echo ""
echo "========================================="
echo "  PACKAGE READY!"
echo "========================================="
echo ""
echo "Package location: $(pwd)/$PACKAGE_DIR"
echo ""
echo "Files included:"
ls -lh "$PACKAGE_DIR"
echo ""
echo "========================================="
echo "  NEXT STEPS:"
echo "========================================="
echo ""
echo "1. Upload to FOG server:"
echo "   scp -r $PACKAGE_DIR root@YOUR_FOG_IP:/tmp/"
echo ""
echo "2. SSH to FOG server and run:"
echo "   cd /tmp/$PACKAGE_DIR"
echo "   sudo bash apply_patches.sh"
echo ""
echo "3. Clear browser cache (Ctrl+Shift+R)"
echo ""
echo "4. Access module:"
echo "   http://YOUR_FOG_IP/fog/management/?node=game"
echo ""
echo "========================================="
