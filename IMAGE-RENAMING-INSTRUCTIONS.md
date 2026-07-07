# Product Image Renaming Instructions

## 📁 Location
**Source Folder:** `C:\Users\Skydrake\Documents\gam`  
**Total Images:** 70 images  

## 📊 Clustering Summary
The images are already grouped by similarity:
- **Cluster 1:** 7 images (same product)
- **Cluster 2:** 6 images (same product)
- **Cluster 3:** 4 images (same product)
- **Cluster 4:** 2 images (same product)
- **Cluster 5:** 2 images (same product)
- **Cluster 6:** 2 images (same product)
- **Single images:** 49 unique products

---

## 🚀 Option 1: Interactive Renaming (RECOMMENDED)

Run this command in PowerShell:
```powershell
cd C:\Users\Skydrake\Documents\gamtech-electronic
.\interactive-rename.ps1
```

**What it does:**
1. Opens each image one by one
2. You type the product name
3. Automatically numbers duplicates (x1, x2, x3...)
4. Shows summary before renaming
5. Renames all files

**Example:**
```
Current: 20260707_140654.jpg
Product name: Logitech-Mouse

Current: 20260707_140759.jpg  
Product name: Logitech-Mouse

Result:
✅ Logitech-Mouse-x1.jpg
✅ Logitech-Mouse-x2.jpg
```

---

## 🛠️ Option 2: Batch Renaming (Edit Script First)

1. Open `auto-rename-products.ps1`
2. Replace product names:
   ```powershell
   "20260707_140654.jpg" = "Logitech-MX-Master"
   "20260707_140759.jpg" = "Logitech-MX-Master"
   ```
3. Run the script:
   ```powershell
   .\auto-rename-products.ps1
   ```

---

## 📝 Naming Guidelines

### ✅ Good Names:
- `Logitech-MX-Master`
- `HP-Laptop-Charger`
- `HDMI-Cable-2m`
- `SanDisk-USB-128GB`

### ❌ Avoid:
- Spaces (use hyphens instead)
- Special characters: `/ \ : * ? " < > |`
- Very long names (keep under 50 characters)

---

## 🔄 Auto-Numbering

When multiple images have the same product name:
- First image: `Product-Name-x1.jpg`
- Second image: `Product-Name-x2.jpg`
- Third image: `Product-Name-x3.jpg`

Single images (no duplicates):
- Just: `Product-Name.jpg` (no x1)

---

## 📋 Product List Template

Based on your previous product catalog, here are suggested names:

### Mice:
- Logitech-M220-Silent
- Logitech-M90
- Logitech-Lift-Vertical
- Logitech-MX-Master-4
- HP-S1500
- HP-M10

### Keyboards:
- Logitech-MX-Keys-Mini
- Logitech-MX-Keys-S
- HP-CS500
- Gaming-Keyboard

### Headphones:
- Logitech-H390
- Logitech-G435
- HP-H231R

### Storage:
- HDD-500GB
- HDD-1TB
- SSD-500GB
- SanDisk-USB-64GB
- SanDisk-MicroSD-128GB

### Networking:
- TP-Link-Router
- Tenda-Router
- WiFi-Adapter-AC1200

### Cables:
- HDMI-Cable
- VGA-Cable
- USB-C-Cable

### Laptop Accessories:
- Laptop-Stand
- Dell-Laptop-Charger
- HP-Laptop-Charger
- Laptop-Battery

### Computer Accessories:
- Mouse-Pad
- Webcam-HD3000
- CMOS-Battery

### Tools:
- RJ45-Crimping-Tool
- Cable-Stripper
- Screwdriver-Kit

---

## ⚠️ Before You Start

1. **Backup:** The gam folder is separate, but make sure you have copies
2. **Check images:** Make sure all images are product photos
3. **Prepare names:** Have product names ready for faster processing

---

## 🚀 Quick Start

**Fastest method:**
```powershell
cd C:\Users\Skydrake\Documents\gamtech-electronic
.\interactive-rename.ps1
```

Then:
1. Type product names as images open
2. Type 'skip' to skip an image
3. Type 'quit' to exit early
4. Confirm final renaming

---

## ✅ After Renaming

Your images will be named like:
```
✅ Logitech-MX-Master-x1.jpg
✅ Logitech-MX-Master-x2.jpg
✅ HP-Laptop-Charger-x1.jpg
✅ HP-Laptop-Charger-x2.jpg
✅ HDMI-Cable.jpg
✅ Mouse-Pad.jpg
```

Much easier to identify and use!

---

**Created:** 2026-07-07  
**Total Images:** 70  
**Clustered Products:** 6 groups  
**Unique Products:** 49  
