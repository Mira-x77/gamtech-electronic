# Auto Product Image Renamer
# Renames images in C:\Users\Skydrake\Documents\gam based on clusters

$sourceFolder = "C:\Users\Skydrake\Documents\gam"

Write-Host "=" * 70 -ForegroundColor Cyan
Write-Host "Product Image Renaming Tool" -ForegroundColor Yellow
Write-Host "=" * 70 -ForegroundColor Cyan
Write-Host "Source: $sourceFolder`n" -ForegroundColor Gray

# Define product mappings based on clusters
# You need to identify what each cluster represents
$productMapping = @{
    # Cluster 1 - 7 images
    "20260707_140654.jpg" = "Product-A"
    "20260707_140759.jpg" = "Product-A"
    "20260707_140851.jpg" = "Product-A"
    "20260707_141805.jpg" = "Product-A"
    "20260707_143106.jpg" = "Product-A"
    "20260707_143110.jpg" = "Product-A"
    "20260707_143350.jpg" = "Product-A"
    
    # Cluster 2 - 6 images
    "20260707_141358.jpg" = "Product-B"
    "20260707_141403.jpg" = "Product-B"
    "20260707_141415.jpg" = "Product-B"
    "20260707_141719.jpg" = "Product-B"
    "20260707_141725.jpg" = "Product-B"
    "20260707_143005.jpg" = "Product-B"
    
    # Cluster 3 - 4 images
    "20260707_140709.jpg" = "Product-C"
    "20260707_140736.jpg" = "Product-C"
    "20260707_140942.jpg" = "Product-C"
    "20260707_141833.jpg" = "Product-C"
    
    # Cluster 4 - 2 images
    "20260706_182110.jpg" = "Product-D"
    "20260707_141020.jpg" = "Product-D"
    
    # Cluster 5 - 2 images
    "20260707_141137.jpg" = "Product-E"
    "20260707_141142.jpg" = "Product-E"
    
    # Cluster 6 - 2 images
    "20260707_142855.jpg" = "Product-F"
    "20260707_142856.jpg" = "Product-F"
    
    # Single images (give them unique names based on timestamp or position)
    "20260706_181952.jpg" = "Product-G"
    "20260706_182136.jpg" = "Product-H"
    "20260707_140404.jpg" = "Product-I"
    "20260707_140411.jpg" = "Product-J"
    "20260707_140418.jpg" = "Product-K"
    "20260707_140425.jpg" = "Product-L"
    "20260707_140431.jpg" = "Product-M"
    "20260707_140438.jpg" = "Product-N"
    "20260707_140447.jpg" = "Product-O"
    "20260707_140504.jpg" = "Product-P"
    "20260707_140512.jpg" = "Product-Q"
    "20260707_140522.jpg" = "Product-R"
    "20260707_140543.jpg" = "Product-S"
    "20260707_140550.jpg" = "Product-T"
    "20260707_140606.jpg" = "Product-U"
    "20260707_140613.jpg" = "Product-V"
    "20260707_140628.jpg" = "Product-W"
    "20260707_140823.jpg" = "Product-X"
    "20260707_140832.jpg" = "Product-Y"
    "20260707_140927.jpg" = "Product-Z"
    "20260707_141025.jpg" = "Product-AA"
    "20260707_141047.jpg" = "Product-AB"
    "20260707_141222.jpg" = "Product-AC"
    "20260707_141227.jpg" = "Product-AD"
    "20260707_141750.jpg" = "Product-AE"
    "20260707_141820.jpg" = "Product-AF"
    "20260707_141857.jpg" = "Product-AG"
    "20260707_141916.jpg" = "Product-AH"
    "20260707_141930.jpg" = "Product-AI"
    "20260707_141947.jpg" = "Product-AJ"
    "20260707_142003.jpg" = "Product-AK"
    "20260707_142022.jpg" = "Product-AL"
    "20260707_142042.jpg" = "Product-AM"
    "20260707_142140.jpg" = "Product-AN"
    "20260707_142152.jpg" = "Product-AO"
    "20260707_142155.jpg" = "Product-AP"
    "20260707_142203.jpg" = "Product-AQ"
    "20260707_142623.jpg" = "Product-AR"
    "20260707_142645.jpg" = "Product-AS"
    "20260707_142701.jpg" = "Product-AT"
    "20260707_142742.jpg" = "Product-AU"
    "20260707_142807.jpg" = "Product-AV"
    "20260707_142919.jpg" = "Product-AW"
    "20260707_142948.jpg" = "Product-AX"
    "20260707_143029.jpg" = "Product-AY"
    "20260707_143142.jpg" = "Product-AZ"
    "20260707_143220.jpg" = "Product-BA"
    "20260707_143358.jpg" = "Product-BB"
}

Write-Host "📸 Found $($productMapping.Count) images in mapping`n" -ForegroundColor Green

# Count products
$productCounts = @{}
foreach ($file in $productMapping.Keys) {
    $productName = $productMapping[$file]
    if (-not $productCounts.ContainsKey($productName)) {
        $productCounts[$productName] = 0
    }
    $productCounts[$productName]++
}

Write-Host "📊 Product Summary:" -ForegroundColor Yellow
$productCounts.GetEnumerator() | Sort-Object Value -Descending | ForEach-Object {
    Write-Host "   $($_.Key): $($_.Value) image(s)" -ForegroundColor Gray
}

Write-Host "`n⚠️  EDIT THIS SCRIPT FIRST!" -ForegroundColor Red
Write-Host "Replace 'Product-A', 'Product-B', etc. with real product names" -ForegroundColor Yellow
Write-Host "Then run it again to perform the renaming.`n" -ForegroundColor Yellow

# Ask to continue
$continue = Read-Host "Continue with renaming? (yes/no)"
if ($continue -ne "yes") {
    Write-Host "❌ Cancelled" -ForegroundColor Red
    exit
}

# Perform renaming
$renamed = 0
$skipped = 0
$productIndices = @{}

foreach ($oldFileName in $productMapping.Keys | Sort-Object) {
    $productName = $productMapping[$oldFileName]
    $oldPath = Join-Path $sourceFolder $oldFileName
    
    if (-not (Test-Path $oldPath)) {
        Write-Host "⚠️  Not found: $oldFileName" -ForegroundColor Yellow
        $skipped++
        continue
    }
    
    # Increment index for this product
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
    
    # Check if already renamed
    if ($oldPath -eq $newPath) {
        Write-Host "✓ Already named: $newFileName" -ForegroundColor DarkGray
        continue
    }
    
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
        Write-Host "❌ Error renaming $oldFileName : $_" -ForegroundColor Red
        $skipped++
    }
}

Write-Host "`n" + ("=" * 70) -ForegroundColor Cyan
Write-Host "✅ Renamed: $renamed files" -ForegroundColor Green
Write-Host "⚠️  Skipped: $skipped files" -ForegroundColor Yellow
Write-Host ("=" * 70) -ForegroundColor Cyan
