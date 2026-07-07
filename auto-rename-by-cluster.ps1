# Fully Automated Renamer - No input needed
# Renames based on cluster position

$sourceFolder = "C:\Users\Skydrake\Documents\gam"

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Automated Product Image Renamer" -ForegroundColor Yellow
Write-Host "========================================`n" -ForegroundColor Cyan

# Auto-generate product names based on position
$productMapping = @{
    # Cluster 1 - 7 images (Product-01)
    "20260707_140654.jpg" = "Product-01"
    "20260707_140759.jpg" = "Product-01"
    "20260707_140851.jpg" = "Product-01"
    "20260707_141805.jpg" = "Product-01"
    "20260707_143106.jpg" = "Product-01"
    "20260707_143110.jpg" = "Product-01"
    "20260707_143350.jpg" = "Product-01"
    
    # Cluster 2 - 6 images (Product-02)
    "20260707_141358.jpg" = "Product-02"
    "20260707_141403.jpg" = "Product-02"
    "20260707_141415.jpg" = "Product-02"
    "20260707_141719.jpg" = "Product-02"
    "20260707_141725.jpg" = "Product-02"
    "20260707_143005.jpg" = "Product-02"
    
    # Cluster 3 - 4 images (Product-03)
    "20260707_140709.jpg" = "Product-03"
    "20260707_140736.jpg" = "Product-03"
    "20260707_140942.jpg" = "Product-03"
    "20260707_141833.jpg" = "Product-03"
    
    # Cluster 4 - 2 images (Product-04)
    "20260706_182110.jpg" = "Product-04"
    "20260707_141020.jpg" = "Product-04"
    
    # Cluster 5 - 2 images (Product-05)
    "20260707_141137.jpg" = "Product-05"
    "20260707_141142.jpg" = "Product-05"
    
    # Cluster 6 - 2 images (Product-06)
    "20260707_142855.jpg" = "Product-06"
    "20260707_142856.jpg" = "Product-06"
    
    # Singles (Product-07 through Product-55)
    "20260706_181952.jpg" = "Product-07"
    "20260706_182136.jpg" = "Product-08"
    "20260707_140404.jpg" = "Product-09"
    "20260707_140411.jpg" = "Product-10"
    "20260707_140418.jpg" = "Product-11"
    "20260707_140425.jpg" = "Product-12"
    "20260707_140431.jpg" = "Product-13"
    "20260707_140438.jpg" = "Product-14"
    "20260707_140447.jpg" = "Product-15"
    "20260707_140504.jpg" = "Product-16"
    "20260707_140512.jpg" = "Product-17"
    "20260707_140522.jpg" = "Product-18"
    "20260707_140543.jpg" = "Product-19"
    "20260707_140550.jpg" = "Product-20"
    "20260707_140606.jpg" = "Product-21"
    "20260707_140613.jpg" = "Product-22"
    "20260707_140628.jpg" = "Product-23"
    "20260707_140823.jpg" = "Product-24"
    "20260707_140832.jpg" = "Product-25"
    "20260707_140927.jpg" = "Product-26"
    "20260707_141025.jpg" = "Product-27"
    "20260707_141047.jpg" = "Product-28"
    "20260707_141222.jpg" = "Product-29"
    "20260707_141227.jpg" = "Product-30"
    "20260707_141750.jpg" = "Product-31"
    "20260707_141820.jpg" = "Product-32"
    "20260707_141857.jpg" = "Product-33"
    "20260707_141916.jpg" = "Product-34"
    "20260707_141930.jpg" = "Product-35"
    "20260707_141947.jpg" = "Product-36"
    "20260707_142003.jpg" = "Product-37"
    "20260707_142022.jpg" = "Product-38"
    "20260707_142042.jpg" = "Product-39"
    "20260707_142140.jpg" = "Product-40"
    "20260707_142152.jpg" = "Product-41"
    "20260707_142155.jpg" = "Product-42"
    "20260707_142203.jpg" = "Product-43"
    "20260707_142623.jpg" = "Product-44"
    "20260707_142645.jpg" = "Product-45"
    "20260707_142701.jpg" = "Product-46"
    "20260707_142742.jpg" = "Product-47"
    "20260707_142807.jpg" = "Product-48"
    "20260707_142919.jpg" = "Product-49"
    "20260707_142948.jpg" = "Product-50"
    "20260707_143029.jpg" = "Product-51"
    "20260707_143142.jpg" = "Product-52"
    "20260707_143220.jpg" = "Product-53"
    "20260707_143358.jpg" = "Product-54"
}

Write-Host "📸 Total images: $($productMapping.Count)" -ForegroundColor Green

# Count products
$productCounts = @{}
foreach ($file in $productMapping.Keys) {
    $prod = $productMapping[$file]
    if (-not $productCounts.ContainsKey($prod)) {
        $productCounts[$prod] = 0
    }
    $productCounts[$prod]++
}

Write-Host "📦 Total products: $($productCounts.Count)`n" -ForegroundColor Green

Write-Host "Products with multiple images:" -ForegroundColor Yellow
$productCounts.GetEnumerator() | Where-Object { $_.Value -gt 1 } | Sort-Object Name | ForEach-Object {
    Write-Host "   $($_.Key): $($_.Value) images" -ForegroundColor Gray
}

Write-Host "`n🔄 Starting rename...`n" -ForegroundColor Cyan

# Perform renaming
$productIndices = @{}
$renamed = 0
$skipped = 0

foreach ($oldFileName in $productMapping.Keys | Sort-Object) {
    $productName = $productMapping[$oldFileName]
    $oldPath = Join-Path $sourceFolder $oldFileName
    
    if (-not (Test-Path $oldPath)) {
        Write-Host "⚠️  Not found: $oldFileName" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    # Increment counter
    if (-not $productIndices.ContainsKey($productName)) {
        $productIndices[$productName] = 0
    }
    $productIndices[$productName]++
    
    # Generate filename
    $index = $productIndices[$productName]
    if ($productCounts[$productName] -eq 1) {
        $newFileName = "$productName.jpg"
    } else {
        $newFileName = "$productName-x$index.jpg"
    }
    
    $newPath = Join-Path $sourceFolder $newFileName
    
    # Check if already correct
    if ($oldPath -eq $newPath) {
        Write-Host "✓ Already named: $newFileName" -ForegroundColor DarkGray
        continue
    }
    
    # Check conflicts
    if (Test-Path $newPath) {
        Write-Host "⚠️  Conflict: $newFileName exists" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    try {
        Rename-Item -Path $oldPath -NewName $newFileName -ErrorAction Stop
        Write-Host "✅ $oldFileName → $newFileName" -ForegroundColor Green
        $renamed++
    } catch {
        Write-Host "❌ Error: $_" -ForegroundColor Red
        $skipped++
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "✅ Renamed: $renamed files" -ForegroundColor Green
Write-Host "⚠️  Skipped: $skipped files" -ForegroundColor Yellow
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "📝 Result: Images renamed Product-01 through Product-54" -ForegroundColor Yellow
Write-Host "   - Products with multiple images have -x1, -x2, etc." -ForegroundColor Gray
Write-Host "   - Single images just have the product number`n" -ForegroundColor Gray
