# 🚨 HTTP 500 Error — Permanent Fix Guide

## ✅ What Was Fixed (Just Now)

1. **Git detached HEAD** → Fixed (now on `main` branch)
2. **Missing logo file references** → Updated `set-logo.php` to use `gamtech-logo.png`
3. **Uncommitted changes** → Committed and pushed to GitHub
4. **Deleted logo files** → Removed from Git (they were already deleted locally)

---

## 🔧 Server-Side Fixes (Do These on Your Host)

### **Step 1: Pull Latest Changes from GitHub**

SSH into your server or use cPanel Terminal, then:

```bash
cd /home/c2423708c/public_html
git pull origin main
```

This will sync the latest fixes to your live server.

---

### **Step 2: Check for PHP Errors in Error Log**

The **error log** will tell you exactly what's breaking:

**Via cPanel:**
- Go to **Errors** in cPanel
- Or check: `/home/c2423708c/public_html/error_log`

**Via SSH/Terminal:**
```bash
tail -50 /home/c2423708c/public_html/error_log
```

Look for lines like:
```
PHP Fatal error: ...
PHP Parse error: ...
```

---

### **Step 3: Common Fixes Based on Error Type**

#### **A. If Error Says: "Failed opening required"**
**Cause:** Missing file (like deleted logo files)

**Fix:**
```bash
cd /home/c2423708c/public_html
git pull origin main  # Get latest code
```

#### **B. If Error Says: "Memory limit"**
**Cause:** PHP ran out of memory

**Fix:** Edit `wp-config.php` and add before `/* That's all, stop editing! */`:
```php
define('WP_MEMORY_LIMIT', '256M');
```

#### **C. If Error Says: "syntax error" or "unexpected token"**
**Cause:** PHP syntax error in a file

**Fix:** The error log will show the **file and line number**. Fix that specific line.

#### **D. If Error Says: Nothing (blank 500)**
**Cause:** `.htaccess` is broken

**Fix:** Replace `.htaccess` content with:
```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
```

---

### **Step 4: Clear All Caches**

After pulling from GitHub:

**Clear WordPress cache:**
```bash
cd /home/c2423708c/public_html
rm -rf wp-content/cache/*
rm -rf wp-content/uploads/wc-logs/*
```

**Clear PHP OpCache** (via cPanel):
- Go to **MultiPHP INI Editor** → **Restart PHP-FPM**

**Or via SSH:**
```bash
killall -USR2 php-fpm
```

---

### **Step 5: Test Each Component**

#### **Test 1: Can PHP run at all?**
Create `test.php` in root:
```php
<?php phpinfo(); ?>
```

Visit: `https://gamtech-electronic.com/test.php`

- ✅ If you see PHP info → PHP works
- ❌ If 500 error → PHP config issue (check php.ini)

#### **Test 2: Can WordPress load?**
Visit: `https://gamtech-electronic.com/wp-admin/`

- ✅ If login page appears → WordPress core works
- ❌ If 500 error → Plugin or theme issue

#### **Test 3: Is the theme the problem?**
Temporarily switch to default theme via database:

```sql
UPDATE wp_options
SET option_value = 'twentytwentyfour'
WHERE option_name = 'template';

UPDATE wp_options
SET option_value = 'twentytwentyfour'
WHERE option_name = 'stylesheet';
```

Visit site:
- ✅ If it works → Your Woodmart child theme is the issue
- ❌ Still broken → Plugin issue

---

### **Step 6: Disable Plugins (If Theme Test Passed)**

Rename plugins folder:
```bash
cd /home/c2423708c/public_html/wp-content
mv plugins plugins_off
```

Visit site:
- ✅ Works now → A plugin was crashing it
- Enable plugins one by one to find the culprit:
  ```bash
  mv plugins_off plugins
  ```
  Then disable plugins via database or re-enable selectively.

---

## 🎯 Most Likely Root Cause (Based on Your History)

You've fixed this **5 times**, which means it's **not a random crash** — it's a **deployment sync issue**.

### **The Pattern:**
1. You push code to GitHub
2. Server auto-pulls via cPanel Git Deploy or `deploy.php`
3. **Something in the pull breaks the site**

### **Why This Happens:**
- **Incomplete Git pulls** (network timeout, permission issues)
- **Missing file dependencies** (like the deleted logo files)
- **Cached opcache** still running old code after pull
- **File permissions** broken after deploy

### **Permanent Solution:**

**Option A: Add Post-Deploy Cache Clear**

Edit `deploy.php` (or your deploy script) and add at the end:

```php
// Clear PHP OpCache after deploy
if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Clear WordPress transients
delete_transient('woodmart_style_storage');
wp_cache_flush();

// Clear file stat cache
clearstatcache(true);
```

**Option B: Use Safer Deploy Process**

Instead of auto-deploy, use **manual pull + verification**:

```bash
cd /home/c2423708c/public_html
git pull origin main
php -l wp-config.php  # Check syntax
php -l wp-content/themes/woodmart-child/functions.php
# If all OK, then reload PHP-FPM
killall -USR2 php-fpm
```

---

## 📊 Quick Diagnosis Checklist

Run these commands on the server to diagnose:

```bash
cd /home/c2423708c/public_html

# 1. Check Git status
git status

# 2. Check PHP syntax in critical files
php -l wp-config.php
php -l wp-content/themes/woodmart-child/functions.php
php -l wp-content/themes/woodmart-child/front-page.php

# 3. Check file permissions
ls -la | grep "wp-config.php"  # Should be 644 or 640
ls -la wp-content/themes/woodmart-child/ | grep "functions.php"  # Should be 644

# 4. Check error log
tail -20 error_log

# 5. Check if WP can load
php -r "require 'wp-load.php'; echo 'WP loads OK';"
```

---

## 🚀 After Server is Fixed

Once the site is back online:

1. **Don't change anything immediately**
2. **Check error_log** to see what caused the crash
3. **Add monitoring:**
   - Set up uptime monitoring (UptimeRobot, etc.)
   - Enable WordPress debug log temporarily:
     ```php
     // In wp-config.php
     define('WP_DEBUG', true);
     define('WP_DEBUG_LOG', true);
     define('WP_DEBUG_DISPLAY', false);
     ```
4. **Document the fix** so you remember for next time

---

## 📞 If Still Broken After All This

1. **Get the exact error** from `error_log`
2. **Check hosting PHP version** (might need PHP 8.0+)
3. **Contact hosting support** with the error log
4. **Rollback to last working commit:**
   ```bash
   git log --oneline -10  # Find last working commit
   git reset --hard <commit-hash>
   git push origin main --force
   ```

---

## ✅ Prevention Checklist

To stop this from happening again:

- [ ] Always test locally before pushing to `main`
- [ ] Use a staging branch for experiments
- [ ] Clear server cache after every deployment
- [ ] Monitor error logs daily
- [ ] Keep backups before major changes
- [ ] Test Git pulls manually before auto-deploying

---

**Created:** 2026-07-06  
**Last Updated:** After fixing logo file references and Git sync issues  
**Status:** Changes pushed to GitHub — server needs to `git pull`
