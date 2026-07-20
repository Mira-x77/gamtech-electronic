#!/bin/bash
# Auto-Recovery Script for gamtech-electronic.com
# Run this when site goes down

echo "=== GAMTECH AUTO RECOVERY ==="
echo "Starting recovery at $(date)"

# Navigate to site directory
cd /home/c2423708c/public_html || exit 1

# Check if we're in a git repository
if [ ! -d .git ]; then
    echo "ERROR: Not a git repository!"
    exit 1
fi

# Save current state
echo "Saving current state..."
git stash save "Auto-recovery backup $(date +%Y%m%d_%H%M%S)"

# Reset to latest origin/main
echo "Resetting to origin/main..."
git fetch origin
git reset --hard origin/main

# Verify critical files exist
echo "Checking critical files..."
CRITICAL_FILES=(
    "wp-includes/load.php"
    "wp-includes/functions.php"
    "wp-config.php"
    "index.php"
)

MISSING_FILES=()
for file in "${CRITICAL_FILES[@]}"; do
    if [ ! -f "$file" ]; then
        echo "  ✗ MISSING: $file"
        MISSING_FILES+=("$file")
    else
        echo "  ✓ Found: $file"
    fi
done

# If WordPress core files are missing, restore them
if [ ${#MISSING_FILES[@]} -gt 0 ]; then
    echo "WARNING: ${#MISSING_FILES[@]} critical files missing!"
    echo "Attempting to restore from git..."
    
    for file in "${MISSING_FILES[@]}"; do
        git checkout HEAD -- "$file" 2>/dev/null || echo "  Could not restore $file from git"
    done
fi

# Clear any caches
echo "Clearing caches..."
if command -v wp &> /dev/null; then
    wp cache flush --allow-root 2>/dev/null || echo "  WP-CLI cache clear failed"
fi

# Check if site is responding
echo "Testing site..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" https://gamtech-electronic.com/)
if [ "$HTTP_CODE" = "200" ]; then
    echo "✓ Site is responding (HTTP $HTTP_CODE)"
else
    echo "✗ Site still down (HTTP $HTTP_CODE)"
fi

echo "Recovery completed at $(date)"
echo "==========================="
