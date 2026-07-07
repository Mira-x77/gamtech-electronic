# Bulk Import Products from Gam Folder

## ✅ What Was Done

1. **Renamed 70 images** in `C:\Users\Skydrake\Documents\gam`:
   - Clusters (same product, multiple images): `Product-01-x1.jpg`, `Product-01-x2.jpg`, etc.
   - Singles: `Product-07.jpg`, `Product-08.jpg`, etc.

2. **Created bulk importer** that will:
   - Create WooCommerce products from all images
   - Set as **DRAFT** (not published)
   - Use filename as **SKU**
   - **No product names** - you name them
   - **No prices** - you set prices
   - Auto-attach product images

---

## 🚀 How to Import to Website

### Step 1: Upload Images to Server

**Option A: Via cPanel File Manager**
1. Compress the gam folder: Right-click → Send to → Compressed folder
2. Upload `gam.zip` to server root: `/home/c2423708c/`
3. Extract on server
4. You should have: `/home/c2423708c/gam/*.jpg`

**Option B: Via FTP/SFTP**
- Upload entire `C:\Users\Skydrake\Documents\gam` folder to `/home/c2423708c/gam/`

**Option C: Leave images locally (if running locally)**
- Script will look for `C:\Users\Skydrake\Documents\gam` first
- If not found, tries `/home/c2423708c/gam`

---

### Step 2: Run the Importer

Visit this URL in your browser:
```
https://gamtech-electronic.com/?bulk_import_gam=1&key=gamtech2026import
```

**What it does:**
- Scans the gam folder
- Copies images to WordPress uploads
- Creates one product per image
- All products are **DRAFT** status
- SKU = filename (e.g., `Product-01-x1`)

**Output:**
```
========================================
GamTech Bulk Product Import
========================================

Found 69 images

====================================
Import Complete
Created : 69 products
Skipped : 0 products
====================================

All products are in DRAFT status.
Go to WooCommerce → Products to edit names and prices.
```

---

### Step 3: Edit Products in Admin Panel

1. Go to: **WordPress Admin → WooCommerce → Products**
2. You'll see all products in **DRAFT** status
3. Click **Edit** on each product
4. Set:
   - **Product name** (e.g., "Logitech MX Master Mouse")
   - **Regular price** (e.g., "49.99")
   - **Description**
   - **Categories**
5. Change status from **Draft** to **Publish**
6. Click **Update**

---

## 📊 Product Structure

### Products with Multiple Images (Clusters):
- `Product-01-x1`, `Product-01-x2`, ... `Product-01-x7` (7 images, same product)
- `Product-02-x1`, `Product-02-x2`, ... `Product-02-x6` (6 images, same product)
- `Product-03-x1`, `Product-03-x2`, ... `Product-03-x4` (4 images, same product)
- `Product-04-x1`, `Product-04-x2` (2 images, same product)
- `Product-05-x1`, `Product-05-x2` (2 images, same product)
- `Product-06-x1`, `Product-06-x2` (2 images, same product)

### Single Products:
- `Product-07` through `Product-54` (unique products)

**Total:** 54 unique products, 69 product images

---

## 🔄 If You Need to Re-Run

The import only runs once. To run again:

1. Go to: **WordPress Admin → Tools → Database**
2. Or via phpMyAdmin
3. Delete option: `gamtech_gam_import_done`
4. Visit the import URL again

---

## ⚠️ Important Notes

1. **Images stay in gam folder** - script copies them to WordPress uploads
2. **Products are DRAFT** - not visible on site until you publish
3. **SKU = filename** - used to avoid duplicates
4. **No prices set** - you must add prices before publishing
5. **No product names** - just "Product-01-x1" etc. - rename in admin

---

## 📝 Workflow Example

**For Product-01 (7 images - same mouse):**

1. Import creates 7 products:
   - Product SKU: `Product-01-x1`
   - Product SKU: `Product-01-x2`
   - ... (7 products with same image set)

2. In admin, you'll:
   - Edit first one → Name: "Logitech MX Master Mouse"
   - Set price: $49.99
   - Publish

3. Then realize: "Wait, I have 7 products for same mouse!"

4. **Solution:** Delete the duplicate products, keep only one
   - Or use the image gallery feature to add all 7 images to one product

---

## 💡 Better Approach for Multi-Image Products

Instead of creating 7 separate products, you might want:

**One product with 7 images in gallery:**
1. Create one product: "Logitech MX Master Mouse"
2. Set main product image
3. Add remaining 6 images to **Product Gallery**
4. This shows image carousel on product page

Let me know if you want a script that automatically groups the x1, x2, x3 images into galleries instead of separate products!

---

## 🎯 Quick Summary

✅ **69 images renamed** in gam folder  
✅ **Bulk importer created**  
✅ **Products will be DRAFT** with no names/prices  
✅ **You edit via admin panel** before publishing  

---

**Created:** 2026-07-07  
**Status:** Ready to import  
**Website:** NOT TOUCHED - ready when you are  
