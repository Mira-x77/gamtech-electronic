# Interactive Product Image Renamer
# Shows each image and asks for product name

Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

$sourceFolder = "C:\Users\Skydrake\Documents\gam"

Write-Host "=" * 70 -ForegroundColor Cyan
Write-Host "Interactive Product Image Renaming Tool" -ForegroundColor Yellow
Write-Host "=" * 70 -ForegroundColor Cyan
Write-Host "Source: $sourceFolder`n" -ForegroundColor Gray

# Get all JPG files
$imageFiles = Get-ChildItem -Path $sourceFolder -Filter "*.jpg" | Sort-Object Name

if ($imageFiles.Count -eq 0) {
    Write-Host "❌ No JPG files found in $sourceFolder" -ForegroundColor Red
    exit
}

Write-Host "📸 Found $($imageFiles.Count) images`n" -ForegroundColor Green

# Store product names
$productMapping = @{}
$productCounts = @{}

# Process each image
$index = 1
foreach ($imageFile in $imageFiles) {
    Write-Host "`n[$index/$($imageFiles.Count)] Current: $($imageFile.Name)" -ForegroundColor Cyan
    
    # Open image in default viewer
    Start-Process $imageFile.FullName
    
    # Ask for product name
    $productName = Read-Host "Product name (or 'skip'/'quit')"
    
    if ($productName -eq "quit") {
        Write-Host "`n⚠️  Exiting..." -ForegroundColor Yellow
        break
    }
    
    if ($productName -eq "skip" -or $productName -eq "") {
        Write-Host "⏭️  Skipped" -ForegroundColor Gray
        $index++
        continue
    }
    
    # Clean product name
    $cleanName = $productName -replace '[\\/:*?"<>|]', '_'
    $cleanName = $cleanName -replace '\s+', '-'
    
    # Track count
    if (-not $productCounts.ContainsKey($cleanName)) {
        $productCounts[$cleanName] = 0
    }
    $productCounts[$cleanName]++
    
    # Store mapping
    $productMapping[$imageFile.Name] = $cleanName
    
    Write-Host "✓ Mapped to: $cleanName" -ForegroundColor Green
    
    $index++
}

# Show summary
Write-Host "`n" + ("=" * 70) -ForegroundColor Cyan
Write-Host "📋 RENAMING SUMMARY" -ForegroundColor Yellow
Write-Host ("=" * 70) -ForegroundColor Cyan

if ($productMapping.Count -eq 0) {
    Write-Host "❌ No files to rename" -ForegroundColor Red
    exit
}

Write-Host "`n📊 Product Counts:" -ForegroundColor Yellow
$productCounts.GetEnumerator() | Sort-Object Key | ForEach-Object {
    Write-Host "   $($_.Key): $($_.Value) image(s)" -ForegroundColor Gray
}

Write-Host "`n📝 Renames:" -ForegroundColor Yellow
$productIndices = @{}

foreach ($oldFileName in $productMapping.Keys | Sort-Object) {
    $productName = $productMapping[$oldFileName]
    
    # Increment index
    if (-not $productIndices.ContainsKey($productName)) {
        $productIndices[$productName] = 0
    }
    $productIndices[$productName]++
    
    # Generate new filename
    $index = $productIndices[$productName]
    if ($productCounts[$productName] -eq 1) {
        $newFileName = "$productName.jpg"
    } else {
        $newFileName = "$productName-x$index.jpg"
    }
    
    Write-Host "   $oldFileName → $newFileName" -ForegroundColor Gray
}

# Confirm
Write-Host ""
$confirm = Read-Host "⚠️  Rename $($productMapping.Count) files? (yes/no)"

if ($confirm -ne "yes") {
    Write-Host "❌ Renaming cancelled" -ForegroundColor Red
    exit
}

# Perform renaming
Write-Host "`n🔄 Renaming files...`n" -ForegroundColor Cyan

$renamed = 0
$skipped = 0
$productIndices = @{}

foreach ($oldFileName in $productMapping.Keys | Sort-Object) {
    $productName = $productMapping[$oldFileName]
    $oldPath = Join-Path $sourceFolder $oldFileName
    
    # Increment index
    if (-not $productIndices.ContainsKey($productName)) {
        $productIndices[$productName] = 0
    }
    $productIndices[$productName]++
    
    # Generate new filename
    $index = $productIndices[$productName]
    if ($productCounts[$productName] -eq 1) {
        $newFileName = "$productName.jpg"
    } else {
        $newFileName = "$productName-x$index.jpg"
    }
    
    $newPath = Join-Path $sourceFolder $newFileName
    
    # Check if target exists
    if (Test-Path $newPath) {
        Write-Host "⚠️  Target exists: $newFileName (skipping)" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    # Rename
    try {
        Rename-Item -Path $oldPath -NewName $newFileName -ErrorAction Stop
        Write-Host "✅ $oldFileName → $newFileName" -ForegroundColor Green
        $renamed++
    } catch {
        Write-Host "❌ Error: $_" -ForegroundColor Red
        $skipped++
    }
}

Write-Host "`n" + ("=" * 70) -ForegroundColor Cyan
Write-Host "✅ Successfully renamed: $renamed files" -ForegroundColor Green
Write-Host "⚠️  Skipped: $skipped files" -ForegroundColor Yellow
Write-Host ("=" * 70) -ForegroundColor Cyan
