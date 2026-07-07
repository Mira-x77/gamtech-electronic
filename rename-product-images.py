#!/usr/bin/env python3
"""
Product Image Analyzer and Renamer
Analyzes product images and renames them by product name with numbering (x1, x2, etc.)
"""

import os
import shutil
from pathlib import Path
from collections import defaultdict

# Source folder
SOURCE_FOLDER = r"C:\Users\Skydrake\Documents\gam"

def get_product_name_from_user(image_path):
    """
    Display image and get product name from user
    For now, we'll analyze the image_clusters.txt file if it exists
    """
    return None

def analyze_and_rename():
    """Main function to analyze and rename images"""
    
    source_path = Path(SOURCE_FOLDER)
    
    if not source_path.exists():
        print(f"❌ Folder not found: {SOURCE_FOLDER}")
        return
    
    # Get all image files
    image_files = sorted([f for f in source_path.glob("*.jpg") if f.is_file()])
    
    print(f"📸 Found {len(image_files)} images")
    print("=" * 60)
    
    # Check if image_clusters.txt exists for grouping hints
    clusters_file = source_path / "image_clusters.txt"
    product_groups = defaultdict(list)
    
    if clusters_file.exists():
        print("📋 Found image_clusters.txt - reading groupings...\n")
        with open(clusters_file, 'r', encoding='utf-8') as f:
            content = f.read()
            # Parse the clusters
            current_group = None
            for line in content.split('\n'):
                line = line.strip()
                if line.startswith('Group') or line.startswith('Cluster'):
                    current_group = line
                    product_groups[current_group] = []
                elif line.endswith('.jpg') and current_group:
                    product_groups[current_group].append(line.strip())
    
    # Manual mapping based on typical product image naming
    print("🔍 Analyzing images...")
    print("\nPlease identify each product. Type the product name for each image.")
    print("For duplicates, they'll automatically be numbered (x1, x2, etc.)\n")
    
    # Store product names
    product_names = {}
    product_counter = defaultdict(int)
    
    for i, img_file in enumerate(image_files, 1):
        print(f"\n[{i}/{len(image_files)}] Current file: {img_file.name}")
        
        # Show which cluster this belongs to if available
        for group, files in product_groups.items():
            if img_file.name in files:
                print(f"   📁 Found in: {group}")
                if files:
                    print(f"   🔗 Grouped with: {', '.join(files[:3])}")
                break
        
        # Get product name from user
        product_name = input("   Product name (or 'skip' to skip, 'quit' to exit): ").strip()
        
        if product_name.lower() == 'quit':
            print("\n⚠️  Exiting without renaming...")
            return
        
        if product_name.lower() == 'skip' or not product_name:
            continue
        
        # Clean product name (remove invalid characters)
        clean_name = "".join(c if c.isalnum() or c in (' ', '-', '_') else '_' for c in product_name)
        clean_name = clean_name.replace(' ', '-')
        
        # Increment counter for this product
        product_counter[clean_name] += 1
        count = product_counter[clean_name]
        
        # Generate new filename
        if count == 1:
            new_name = f"{clean_name}.jpg"
        else:
            new_name = f"{clean_name}-x{count}.jpg"
        
        product_names[img_file] = new_name
    
    # Show summary
    print("\n" + "=" * 60)
    print("📋 RENAMING SUMMARY")
    print("=" * 60)
    
    if not product_names:
        print("❌ No files to rename")
        return
    
    for old_file, new_name in product_names.items():
        print(f"✏️  {old_file.name} → {new_name}")
    
    # Confirm
    confirm = input(f"\n⚠️  Rename {len(product_names)} files? (yes/no): ").strip().lower()
    
    if confirm != 'yes':
        print("❌ Renaming cancelled")
        return
    
    # Perform renaming
    print("\n🔄 Renaming files...")
    renamed_count = 0
    
    for old_file, new_name in product_names.items():
        new_path = old_file.parent / new_name
        
        # Handle name conflicts
        if new_path.exists() and new_path != old_file:
            print(f"⚠️  Skipping {old_file.name} - {new_name} already exists")
            continue
        
        try:
            old_file.rename(new_path)
            renamed_count += 1
            print(f"✅ Renamed: {old_file.name} → {new_name}")
        except Exception as e:
            print(f"❌ Error renaming {old_file.name}: {e}")
    
    print("\n" + "=" * 60)
    print(f"✅ Successfully renamed {renamed_count} files!")
    print("=" * 60)

if __name__ == "__main__":
    print("=" * 60)
    print("📸 Product Image Renaming Tool")
    print("=" * 60)
    print(f"Source folder: {SOURCE_FOLDER}\n")
    
    analyze_and_rename()
