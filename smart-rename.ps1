# Smart Product Renamer - Uses cluster info and prompts for product names
# Processes images by cluster so you name each product once

$sourceFolder = "C:\Users\Skydrake\Documents\gam"

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Smart Product Image Renamer" -ForegroundColor Yellow
Write-Host "========================================`n" -ForegroundColor Cyan

# Define clusters from image_clusters.txt
$clusters = @{
    "Cluster-1" = @(
        "20260707_140654.jpg",
        "20260707_140759.jpg",
        "20260707_140851.jpg",
        "20260707_141805.jpg",
        "20260707_143106.jpg",
        "20260707_143110.jpg",
        "20260707_143350.jpg"
    )
    "Cluster-2" = @(
        "20260707_141358.jpg",
        "20260707_141403.jpg",
        "20260707_141415.jpg",
        "20260707_141719.jpg",
        "20260707_141725.jpg",
        "20260707_143005.jpg"
    )
    "Cluster-3" = @(
        "20260707_140709.jpg",
        "20260707_140736.jpg",
        "20260707_140942.jpg",
        "20260707_141833.jpg"
    )
    "Cluster-4" = @(
        "20260706_182110.jpg",
        "20260707_141020.jpg"
    )
    "Cluster-5" = @(
        "20260707_141137.jpg",
        "20260707_141142.jpg"
    )
    "Cluster-6" = @(
        "20260707_142855.jpg",
        "20260707_142856.jpg"
    )
}

$singles = @(
    "20260706_181952.jpg",
    "20260706_182136.jpg",
    "20260707_140404.jpg",
    "20260707_140411.jpg",
    "20260707_140418.jpg",
    "20260707_140425.jpg",
    "20260707_140431.jpg",
    "20260707_140438.jpg",
    "20260707_140447.jpg",
    "20260707_140504.jpg",
    "20260707_140512.jpg",
    "20260707_140522.jpg",
    "20260707_140543.jpg",
    "20260707_140550.jpg",
    "20260707_140606.jpg",
    "20260707_140613.jpg",
    "20260707_140628.jpg",
    "20260707_140823.jpg",
    "20260707_140832.jpg",
    "20260707_140927.jpg",
    "20260707_141025.jpg",
    "20260707_141047.jpg",
    "20260707_141222.jpg",
    "20260707_141227.jpg",
    "20260707_141750.jpg",
    "20260707_141820.jpg",
    "20260707_141857.jpg",
    "20260707_141916.jpg",
    "20260707_141930.jpg",
    "20260707_141947.jpg",
    "20260707_142003.jpg",
    "20260707_142022.jpg",
    "20260707_142042.jpg",
    "20260707_142140.jpg",
    "20260707_142152.jpg",
    "20260707_142155.jpg",
    "20260707_142203.jpg",
    "20260707_142623.jpg",
    "20260707_142645.jpg",
    "20260707_142701.jpg",
    "20260707_142742.jpg",
    "20260707_142807.jpg",
    "20260707_142919.jpg",
    "20260707_142948.jpg",
    "20260707_143029.jpg",
    "20260707_143142.jpg",
    "20260707_143220.jpg",
    "20260707_143358.jpg"
)

$productMapping = @{}

# Process clusters first (multiple images per product)
Write-Host "📦 Processing Clusters (multiple images per product):`n" -ForegroundColor Yellow

foreach ($clusterName in $clusters.Keys | Sort-Object) {
    $images = $clusters[$clusterName]
    Write-Host "[$clusterName] - $($images.Count) images" -ForegroundColor Cyan
    Write-Host "   Files: $($images[0]), $($images[1])..." -ForegroundColor Gray
    
    # Open first image to see what product it is
    $firstImage = Join-Path $sourceFolder $images[0]
    if (Test-Path $firstImage) {
        Start-Process $firstImage
        Start-Sleep -Milliseconds 500
    }
    
    $productName = Read-Host "   Product name"
    
    if ($productName -and $productName -ne "skip" -and $productName -ne "quit") {
        $cleanName = $productName -replace '[\\/:*?"<>|]', '_' -replace '\s+', '-'
        foreach ($img in $images) {
            $productMapping[$img] = $cleanName
        }
        Write-Host "   ✓ Mapped $($images.Count) images to: $cleanName`n" -ForegroundColor Green
    } elseif ($productName -eq "quit") {
        break
    } else {
        Write-Host "   ⏭️  Skipped`n" -ForegroundColor Gray
    }
}

# Process singles
Write-Host "`n📄 Processing Single Images:`n" -ForegroundColor Yellow
Write-Host "Remaining: $($singles.Count) unique products`n" -ForegroundColor Gray

$singleIndex = 1
foreach ($singleImg in $singles) {
    Write-Host "[$singleIndex/$($singles.Count)] $singleImg" -ForegroundColor Cyan
    
    $singlePath = Join-Path $sourceFolder $singleImg
    if (Test-Path $singlePath) {
        Start-Process $singlePath
        Start-Sleep -Milliseconds 500
    }
    
    $productName = Read-Host "   Product name (or 'skip'/'quit')"
    
    if ($productName -eq "quit") {
        break
    } elseif ($productName -and $productName -ne "skip") {
        $cleanName = $productName -replace '[\\/:*?"<>|]', '_' -replace '\s+', '-'
        $productMapping[$singleImg] = $cleanName
        Write-Host "   ✓ $cleanName`n" -ForegroundColor Green
    } else {
        Write-Host "   ⏭️  Skipped`n" -ForegroundColor Gray
    }
    
    $singleIndex++
}

# Summary
Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "📋 SUMMARY" -ForegroundColor Yellow
Write-Host "========================================`n" -ForegroundColor Cyan

if ($productMapping.Count -eq 0) {
    Write-Host "❌ No files to rename" -ForegroundColor Red
    exit
}

Write-Host "Total files to rename: $($productMapping.Count)`n" -ForegroundColor Green

# Count products
$productCounts = @{}
foreach ($img in $productMapping.Keys) {
    $prod = $productMapping[$img]
    if (-not $productCounts.ContainsKey($prod)) {
        $productCounts[$prod] = 0
    }
    $productCounts[$prod]++
}

Write-Host "Products:" -ForegroundColor Yellow
$productCounts.GetEnumerator() | Sort-Object Value -Descending | ForEach-Object {
    Write-Host "   $($_.Key): $($_.Value) image(s)" -ForegroundColor Gray
}

# Confirm
Write-Host ""
$confirm = Read-Host "Rename $($productMapping.Count) files? (yes/no)"

if ($confirm -ne "yes") {
    Write-Host "`n❌ Cancelled" -ForegroundColor Red
    exit
}

# Perform renaming
Write-Host "`n🔄 Renaming...`n" -ForegroundColor Cyan

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
    
    # Check if already correct name
    if ($oldPath -eq $newPath) {
        Write-Host "✓ $newFileName (already named)" -ForegroundColor DarkGray
        continue
    }
    
    # Check conflicts
    if (Test-Path $newPath) {
        Write-Host "⚠️  Conflict: $newFileName already exists" -ForegroundColor Yellow
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
