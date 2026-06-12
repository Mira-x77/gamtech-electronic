# Production Safety Rules — gamtech-electronic.com

## Project Overview

This is the **live production** WordPress + WooCommerce website for **gamtech-electronic.com**.

- **Server path:** `/home/c2423708c/public_html`
- **Git branch:** `main`
- **Remote:** `https://github.com/Mira-x77/gamtech-electronic.git`
- GitHub and the server are currently synchronized.

---

## Architecture

The repository contains the full WordPress installation:
- `wp-admin/`
- `wp-includes/`
- `wp-content/plugins/` — including WooCommerce, Elementor, Woodmart Core, etc.
- `wp-content/themes/` — Woodmart + woodmart-child (active child theme)
- `wp-content/uploads/`

WooCommerce is active. Products, orders, and customer data live in the database and must never be touched.

---

## Hard Rules — NEVER Do These

- Do NOT reinitialize Git
- Do NOT delete the `.git` directory
- Do NOT rename the `main` branch
- Do NOT change the remote origin
- Do NOT change hosting settings
- Do NOT delete WordPress core files
- Do NOT delete plugins without explicit permission
- Do NOT modify `wp-config.php` unless explicitly instructed
- Do NOT modify the database structure
- Do NOT run destructive commands
- Do NOT use `git reset --hard`
- Do NOT use `git clean -fd`
- Do NOT force push (`git push --force`)
- Do NOT rewrite Git history
- Do NOT change WooCommerce tables
- Do NOT remove uploaded media
- Do NOT replace the site with another framework
- Do NOT convert to React, Next.js, Laravel, or headless WordPress unless explicitly requested

---

## Development Philosophy

Make the **smallest possible changes**. Preserve:
- Existing functionality
- WooCommerce and all store data
- Existing URLs and SEO
- Existing database, products, and orders
- Existing theme (unless specifically instructed otherwise)

---

## Code Modification Rules

Before editing any file:
1. Inspect the existing code first
2. Reuse existing patterns and conventions
3. Avoid unnecessary refactoring
4. Prefer modifying existing files over creating new architectures
5. Keep all changes backward compatible
6. Explain potentially risky changes before making them

---

## Git Workflow

After every change:
```bash
git add .
git commit -m "Describe the change"
git push origin main
```

**Never** use `git push --force`.

---

## Priority Order

1. Stability
2. Data preservation
3. Compatibility
4. Maintainability
5. New features

Breaking the live store is **unacceptable** under any circumstances.
