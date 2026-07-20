#!/bin/bash
# Automatic Site Monitor and Recovery
# This runs every 5 minutes via cron to check if site is down and auto-fix it

SITE_URL="https://gamtech-electronic.com"
SITE_DIR="/home/c2423708c/public_html"
LOG_FILE="/home/c2423708c/site-monitor.log"

# Function to log messages
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Check if site is responding
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$SITE_URL" 2>/dev/null)

# If site is down (500 error or timeout)
if [ "$HTTP_CODE" != "200" ]; then
    log_message "⚠️  SITE DOWN DETECTED (HTTP $HTTP_CODE) - Starting auto-recovery..."
    
    cd "$SITE_DIR" || exit 1
    
    # Step 1: Check for missing critical files
    CRITICAL_FILES=("wp-includes/load.php" "wp-includes/functions.php" "index.php")
    MISSING=0
    
    for file in "${CRITICAL_FILES[@]}"; do
        if [ ! -f "$file" ]; then
            log_message "   Missing: $file"
            MISSING=1
        fi
    done
    
    # Step 2: If files are missing, restore from git
    if [ $MISSING -eq 1 ]; then
        log_message "   Restoring files from git..."
        
        # Fetch latest from origin
        git fetch origin main >> "$LOG_FILE" 2>&1
        
        # Restore missing files
        for file in "${CRITICAL_FILES[@]}"; do
            if [ ! -f "$file" ]; then
                git checkout origin/main -- "$file" >> "$LOG_FILE" 2>&1
                log_message "   Restored: $file"
            fi
        done
    fi
    
    # Step 3: Reset to origin/main if still broken
    log_message "   Resetting to origin/main..."
    git reset --hard origin/main >> "$LOG_FILE" 2>&1
    
    # Step 4: Clear PHP cache/opcache if possible
    if command -v wp &> /dev/null; then
        wp cache flush --allow-root --path="$SITE_DIR" >> "$LOG_FILE" 2>&1
    fi
    
    # Step 5: Check if site is back up
    sleep 3
    NEW_HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$SITE_URL" 2>/dev/null)
    
    if [ "$NEW_HTTP_CODE" = "200" ]; then
        log_message "✅ SITE RECOVERED SUCCESSFULLY (HTTP $NEW_HTTP_CODE)"
    else
        log_message "❌ RECOVERY FAILED (HTTP $NEW_HTTP_CODE) - Manual intervention required"
    fi
else
    # Site is up - log success (only once per hour to avoid log spam)
    CURRENT_MINUTE=$(date '+%M')
    if [ "$CURRENT_MINUTE" = "00" ]; then
        log_message "✓ Site healthy (HTTP $HTTP_CODE)"
    fi
fi

# Cleanup old logs (keep last 1000 lines)
if [ -f "$LOG_FILE" ]; then
    tail -n 1000 "$LOG_FILE" > "${LOG_FILE}.tmp" && mv "${LOG_FILE}.tmp" "$LOG_FILE"
fi
