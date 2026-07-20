#!/bin/bash
# Setup script for automatic site monitoring
# Run this ONCE on the server to set up auto-recovery

echo "=== GAMTECH AUTO-MONITOR SETUP ==="
echo ""

SITE_DIR="/home/c2423708c/public_html"
MONITOR_SCRIPT="$SITE_DIR/auto-monitor.sh"

# Step 1: Make monitor script executable
echo "Making auto-monitor.sh executable..."
chmod +x "$MONITOR_SCRIPT"
echo "✓ Done"

# Step 2: Check if cron job already exists
echo ""
echo "Checking existing cron jobs..."
CRON_EXISTS=$(crontab -l 2>/dev/null | grep -c "auto-monitor.sh")

if [ "$CRON_EXISTS" -gt 0 ]; then
    echo "⚠️  Auto-monitor cron job already exists"
    echo ""
    read -p "Do you want to replace it? (y/n): " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo "Setup cancelled."
        exit 0
    fi
    
    # Remove existing cron job
    crontab -l 2>/dev/null | grep -v "auto-monitor.sh" | crontab -
    echo "✓ Removed old cron job"
fi

# Step 3: Add new cron job (runs every 5 minutes)
echo ""
echo "Adding cron job to run every 5 minutes..."
(crontab -l 2>/dev/null; echo "*/5 * * * * $MONITOR_SCRIPT >/dev/null 2>&1") | crontab -
echo "✓ Cron job added"

# Step 4: Verify cron job
echo ""
echo "Current cron jobs:"
crontab -l | grep "auto-monitor"

# Step 5: Test the monitor script
echo ""
echo "Testing monitor script..."
"$MONITOR_SCRIPT"

if [ -f "/home/c2423708c/site-monitor.log" ]; then
    echo ""
    echo "✓ Monitor script working! Last log entry:"
    tail -n 3 /home/c2423708c/site-monitor.log
else
    echo "⚠️  Log file not created - there may be an issue"
fi

echo ""
echo "=== SETUP COMPLETE ==="
echo ""
echo "📊 Monitor log location: /home/c2423708c/site-monitor.log"
echo "🔧 The site will now be checked every 5 minutes"
echo "🚨 If the site goes down, it will auto-recover within 5 minutes"
echo ""
echo "To view monitor logs: tail -f /home/c2423708c/site-monitor.log"
echo "To disable monitoring: crontab -e (then delete the auto-monitor line)"
echo ""
