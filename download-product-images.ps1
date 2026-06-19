# Product Image Downloader - DuckDuckGo Image Search
# Downloads product images using DuckDuckGo image API

$outputFolder = "c:\Users\ira\Documents\gamtech-electronic\product-images"

$products = @(
    # MOUSE
    "Logitech M220 Silent mouse",
    "Logitech M90 mouse",
    "Logitech Lift Vertical Ergonomic Mouse",
    "Logitech MX Master 4 mouse",
    "Logitech Pro X Superlight 2 mouse",
    "Logitech G502 X mouse",
    "Logitech Signature M650L mouse",
    "HP S1500 mouse",
    "HP M10 mouse",
    "HP DM10 mouse",
    "Lenovo 150WL Wireless Mouse",

    # HEADPHONES & AUDIO
    "Logitech H390 USB Headset",
    "Logitech G435 Wireless Gaming Headset",
    "HP H231R Headset",
    "Tangbo C3U USB Headset",

    # KEYBOARDS
    "Logitech MX Keys Mini keyboard",
    "Logitech MX Keys S keyboard",
    "Logitech BT Mini Keyboard LED",
    "Logitech Pebble 2 Combo keyboard mouse",
    "Foldable Keyboard with Touchpad",
    "Logitech Gaming Keyboard",
    "JK100F Mechanical Keyboard",
    "HP CS500 Keyboard",
    "Mini Wireless Keyboard",

    # STORAGE - HDD
    "500GB Internal Hard Drive HDD",
    "1TB Internal Hard Drive HDD",
    "2TB Internal Hard Drive HDD",
    "Portable HDD 1TB external hard drive",
    "Portable HDD 2TB external hard drive",

    # STORAGE - SSD
    "500GB SSD solid state drive",
    "1TB SSD solid state drive",
    "NVMe M.2 SSD",
    "Lexar NM620 512GB NVMe SSD",

    # STORAGE - External
    "2TB External HDD USB",

    # USB Flash Drives
    "ZNY 4GB USB Flash Drive",
    "ZNY 8GB USB Flash Drive",
    "SanDisk 16GB USB Flash Drive",
    "SanDisk 64GB USB Flash Drive Cruzer",
    "SanDisk 128GB USB Flash Drive Ultra",
    "SanDisk 256GB USB Flash Drive",
    "SanDisk 512GB USB Flash Drive",

    # Memory Cards
    "SanDisk MicroSD 16GB memory card",
    "SanDisk MicroSD 64GB memory card",
    "SanDisk MicroSD 128GB memory card",
    "SanDisk MicroSD 256GB memory card",
    "SanDisk MicroSD 512GB memory card",

    # RAM
    "Laptop DDR4 RAM memory",
    "Desktop DDR4 RAM memory",

    # NETWORKING - Routers
    "TP-Link WiFi Router",
    "Tenda WiFi Router",
    "Mercury WiFi Router",

    # NETWORKING - Access Points
    "Ubiquiti NanoStation M5 Loco",

    # NETWORKING - Wi-Fi Adapters
    "BLGP2500M USB WiFi Adapter",
    "AC1200 Dual Band USB WiFi Adapter",
    "AX900 USB WiFi Adapter",
    "G146 USB WiFi Adapter",

    # NETWORKING - Accessories
    "CAT6 LAN Network Cable",
    "Wireless Display Dongle HDMI",

    # CABLES - HDMI
    "HDMI Cable 4K",
    "HDMI Switch 3 port",

    # CABLES - VGA
    "VGA Cable monitor",
    "HDMI to VGA Converter adapter",

    # CABLES - USB-C
    "HDMI to Type-C Converter cable",
    "Type-C to HDMI Converter cable",

    # LAPTOP ACCESSORIES
    "Laptop Stand aluminum",
    "Laptop Stand with Cooling Fan",
    "USB-C Docking Station laptop",
    "Laptop Internal Battery replacement",
    "Laptop Removable Battery",
    "Dell Laptop Charger adapter",
    "HP Laptop Charger adapter",
    "Acer Laptop Charger adapter",
    "Lenovo Laptop Charger adapter",
    "Universal Laptop Charger 65W",

    # COMPUTER ACCESSORIES
    "Gaming Mouse Pad desk mat",
    "Microsoft LifeCam HD3000 Webcam",
    "Lenovo ThinkPlus KO1C USB hub",
    "CMOS Battery CR2032",
    "Thermal Paste CPU grease",

    # TOOLS & REPAIR
    "RJ45 Crimping Tool network",
    "Cable Stripper wire cutter",
    "Network Tool Kit RJ45",
    "Electric Screwdriver set",
    "Computer Repair Tool Kit",

    # ADAPTERS & HUBS
    "USB Hub multi port adapter",
    "USB Network Ethernet Adapter",
    "HDMI Adapter mini",
    "Type-C USB Hub multiport",
    "VGA HDMI Video Adapter",
    "Multi Port USB Hub"
)

# File name mapping (product name -> safe file name)
$fileNames = @(
    "Logitech-M220-Silent",
    "Logitech-M90",
    "Logitech-Lift-Vertical",
    "Logitech-MX-Master-4",
    "Logitech-Pro-X-Superlight-2",
    "Logitech-G502-X",
    "Logitech-Signature-M650L",
    "HP-S1500-Mouse",
    "HP-M10-Mouse",
    "HP-DM10-Mouse",
    "Lenovo-150WL",
    "Logitech-H390",
    "Logitech-G435",
    "HP-H231R",
    "Tangbo-C3U",
    "Logitech-MX-Keys-Mini",
    "Logitech-MX-Keys-S",
    "BT-Mini-Keyboard-LED",
    "Logitech-Pebble-2-Combo",
    "Foldable-Keyboard-Touchpad",
    "Logitech-Gaming-Keyboard",
    "JK100F-Keyboard",
    "HP-CS500-Keyboard",
    "Mini-Keyboard-Wireless",
    "HDD-500GB",
    "HDD-1TB",
    "HDD-2TB",
    "Portable-HDD-1TB",
    "Portable-HDD-2TB",
    "SSD-500GB",
    "SSD-1TB",
    "NVMe-SSD",
    "Lexar-NM620-512GB",
    "Storage-25M3-2TB",
    "ZNY-4GB-USB",
    "ZNY-8GB-USB",
    "SanDisk-16GB-USB",
    "SanDisk-64GB-USB",
    "SanDisk-128GB-USB",
    "SanDisk-256GB-USB",
    "SanDisk-512GB-USB",
    "SanDisk-MicroSD-16GB",
    "SanDisk-MicroSD-64GB",
    "SanDisk-MicroSD-128GB",
    "SanDisk-MicroSD-256GB",
    "SanDisk-MicroSD-512GB",
    "Laptop-RAM",
    "Desktop-RAM",
    "TP-Link-Router",
    "Tenda-Router",
    "Mercury-Router",
    "NanoStation-M5-Loco",
    "BLGP2500M-WiFi-Adapter",
    "AC1200-WiFi-Adapter",
    "AX900-WiFi-Adapter",
    "G146-WiFi-Adapter",
    "LAN-Cable-CAT6",
    "Wireless-Display-Dongle",
    "HDMI-Cable",
    "HDMI-Switch",
    "VGA-Cable",
    "HDMI-to-VGA-Converter",
    "HDMI-to-TypeC-Converter",
    "TypeC-to-HDMI-Converter",
    "Laptop-Stand",
    "Laptop-Stand-Cooler",
    "Dock-Station",
    "Laptop-Battery-Internal",
    "Laptop-Battery-Removable",
    "Dell-Laptop-Charger",
    "HP-Laptop-Charger",
    "Acer-Laptop-Charger",
    "Lenovo-Laptop-Charger",
    "Universal-Laptop-Charger",
    "Mouse-Pad",
    "Webcam-HD3000",
    "ThinkPlus-KO1C",
    "CMOS-Battery",
    "HY880-Thermal-Paste",
    "RJ45-Crimping-Tool",
    "Cable-Stripper",
    "Network-Tool-Kit",
    "Electric-Screwdriver",
    "Computer-Repair-Toolkit",
    "USB-Adapter-Hub",
    "Network-Adapter-USB",
    "HDMI-Adapter",
    "TypeC-Adapter-Hub",
    "Video-Adapter-VGA-HDMI",
    "Multi-Port-USB-Hub"
)

$headers = @{
    "User-Agent" = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
    "Accept" = "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
    "Accept-Language" = "en-US,en;q=0.5"
}

function Get-DDGImages($query) {
    # Step 1: Get vqd token
    $searchUrl = "https://duckduckgo.com/?q=" + [Uri]::EscapeUriString($query) + "&iax=images&ia=images"
    try {
        $r = Invoke-WebRequest -Uri $searchUrl -Headers $headers -UseBasicParsing -TimeoutSec 15 -ErrorAction Stop
        $vqd = [regex]::Match($r.Content, 'vqd=([^&"]+)').Groups[1].Value
        if (-not $vqd) { return $null }

        # Step 2: Query the images API
        $apiUrl = "https://duckduckgo.com/i.js?l=en-us&o=json&q=" + [Uri]::EscapeUriString($query) + "&vqd=$vqd&f=,,,,,&p=1"
        $apiHeaders = $headers.Clone()
        $apiHeaders["Referer"] = $searchUrl
        $apiHeaders["Accept"] = "application/json, text/javascript, */*"
        $apiHeaders["X-Requested-With"] = "XMLHttpRequest"

        $api = Invoke-WebRequest -Uri $apiUrl -Headers $apiHeaders -UseBasicParsing -TimeoutSec 15 -ErrorAction Stop
        $json = $api.Content | ConvertFrom-Json
        return $json.results
    } catch {
        return $null
    }
}

function Download-Image($url, $outFile) {
    try {
        $imgHeaders = @{
            "User-Agent" = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
            "Referer" = "https://duckduckgo.com/"
        }
        Invoke-WebRequest -Uri $url -Headers $imgHeaders -OutFile $outFile -TimeoutSec 20 -ErrorAction Stop
        if ((Test-Path $outFile) -and (Get-Item $outFile).Length -gt 3000) {
            return $true
        }
        if (Test-Path $outFile) { Remove-Item $outFile -Force }
        return $false
    } catch {
        if (Test-Path $outFile) { Remove-Item $outFile -Force }
        return $false
    }
}

Write-Host "================================================" -ForegroundColor Magenta
Write-Host "  GAMTECH Product Image Downloader" -ForegroundColor Magenta
Write-Host "  Total products: $($products.Count)" -ForegroundColor Magenta
Write-Host "  Output: $outputFolder" -ForegroundColor Magenta
Write-Host "================================================" -ForegroundColor Magenta

$success = 0
$failed = 0
$failed_list = @()

for ($i = 0; $i -lt $products.Count; $i++) {
    $product = $products[$i]
    $fileName = $fileNames[$i]
    $outFile = Join-Path $outputFolder "$fileName.jpg"

    if (Test-Path $outFile) {
        Write-Host "[$($i+1)/$($products.Count)] SKIP (exists): $fileName" -ForegroundColor Yellow
        $success++
        continue
    }

    Write-Host "[$($i+1)/$($products.Count)] Searching: $product" -ForegroundColor Cyan -NoNewline

    $results = Get-DDGImages $product
    $downloaded = $false

    if ($results -and $results.Count -gt 0) {
        foreach ($result in ($results | Select-Object -First 5)) {
            $imgUrl = $result.image
            if (-not $imgUrl) { continue }

            # Determine extension
            $ext = "jpg"
            if ($imgUrl -match '\.(png)(\?|$)') { $ext = "png" }
            elseif ($imgUrl -match '\.(webp)(\?|$)') { $ext = "webp" }
            elseif ($imgUrl -match '\.(jpeg)(\?|$)') { $ext = "jpg" }

            $finalFile = Join-Path $outputFolder "$fileName.$ext"
            if ($ext -ne "jpg") { $outFile = $finalFile }

            if (Download-Image $imgUrl $outFile) {
                $size = [math]::Round((Get-Item $outFile).Length / 1024, 1)
                Write-Host " -> OK ($size KB)" -ForegroundColor Green
                $downloaded = $true
                break
            }
        }
    }

    if ($downloaded) {
        $success++
    } else {
        Write-Host " -> FAILED" -ForegroundColor Red
        $failed++
        $failed_list += $product
    }

    Start-Sleep -Milliseconds 800
}

Write-Host ""
Write-Host "================================================" -ForegroundColor Magenta
Write-Host "  DONE: $success downloaded, $failed failed" -ForegroundColor Magenta
Write-Host "================================================" -ForegroundColor Magenta

if ($failed_list.Count -gt 0) {
    Write-Host ""
    Write-Host "Failed:" -ForegroundColor Red
    $failed_list | ForEach-Object { Write-Host "  - $_" -ForegroundColor Red }
}
