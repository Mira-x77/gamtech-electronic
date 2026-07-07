$s="C:\Users\Skydrake\Documents\gam"
$singles=@{
"20260706_181952.jpg"="Product-07"
"20260706_182136.jpg"="Product-08"
"20260707_140404.jpg"="Product-09"
"20260707_140411.jpg"="Product-10"
"20260707_140418.jpg"="Product-11"
"20260707_140425.jpg"="Product-12"
"20260707_140431.jpg"="Product-13"
"20260707_140438.jpg"="Product-14"
"20260707_140447.jpg"="Product-15"
"20260707_140504.jpg"="Product-16"
"20260707_140512.jpg"="Product-17"
"20260707_140522.jpg"="Product-18"
"20260707_140543.jpg"="Product-19"
"20260707_140550.jpg"="Product-20"
"20260707_140606.jpg"="Product-21"
"20260707_140613.jpg"="Product-22"
"20260707_140628.jpg"="Product-23"
"20260707_140823.jpg"="Product-24"
"20260707_140832.jpg"="Product-25"
"20260707_140927.jpg"="Product-26"
"20260707_141025.jpg"="Product-27"
"20260707_141047.jpg"="Product-28"
"20260707_141222.jpg"="Product-29"
"20260707_141227.jpg"="Product-30"
"20260707_141750.jpg"="Product-31"
"20260707_141820.jpg"="Product-32"
"20260707_141857.jpg"="Product-33"
"20260707_141916.jpg"="Product-34"
"20260707_141930.jpg"="Product-35"
"20260707_141947.jpg"="Product-36"
"20260707_142003.jpg"="Product-37"
"20260707_142022.jpg"="Product-38"
"20260707_142042.jpg"="Product-39"
"20260707_142140.jpg"="Product-40"
"20260707_142152.jpg"="Product-41"
"20260707_142155.jpg"="Product-42"
"20260707_142203.jpg"="Product-43"
"20260707_142623.jpg"="Product-44"
"20260707_142645.jpg"="Product-45"
"20260707_142701.jpg"="Product-46"
"20260707_142742.jpg"="Product-47"
"20260707_142807.jpg"="Product-48"
"20260707_142919.jpg"="Product-49"
"20260707_142948.jpg"="Product-50"
"20260707_143029.jpg"="Product-51"
"20260707_143142.jpg"="Product-52"
"20260707_143220.jpg"="Product-53"
"20260707_143358.jpg"="Product-54"
}
$r=0
$singles.Keys|sort|%{
  $old=$_
  $new=$singles[$_]+".jpg"
  $oldPath=Join-Path $s $old
  $newPath=Join-Path $s $new
  if((Test-Path $oldPath) -and !( Test-Path $newPath)){
    Rename-Item $oldPath $new
    Write-Host "OK: $old -> $new"
    $r++
  }
}
Write-Host "Done: $r singles renamed" -ForegroundColor Cyan
