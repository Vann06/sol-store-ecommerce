$pdfPath = 'C:\Windows\Temp\export_check_fast_debug.pdf'
if (-Not (Test-Path $pdfPath)) { Write-Output "PDF not found at $pdfPath"; exit 1 }
[byte[]]$bytes = [System.IO.File]::ReadAllBytes($pdfPath)
$pngSig = [byte[]](0x89,0x50,0x4E,0x47,0x0D,0x0A,0x1A,0x0A)
$found = $false
for ($i = 0; $i -le $bytes.Length - $pngSig.Length; $i++) {
    $slice = $bytes[$i..($i + $pngSig.Length - 1)]
    if ($slice -ceq $pngSig) { Write-Output "PNG signature found at byte index: $i"; $found = $true; break }
}
if (-not $found) { Write-Output "PNG signature NOT found" }

# Search for textual markers
try {
    $txt = [System.Text.Encoding]::UTF8.GetString($bytes)
    if ($txt -match 'Reporte') { Write-Output "Text 'Reporte' found in PDF" } else { Write-Output "Text 'Reporte' NOT found in PDF" }
    if ($txt -match 'Ventas') { Write-Output "Text 'Ventas' found in PDF" } else { Write-Output "Text 'Ventas' NOT found in PDF" }
} catch {
    Write-Output "Failed to decode PDF bytes as UTF8 for text search"
}

# Show file sizes of debug assets if present
$assets = @( 
    'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartPng.png',
    'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartSvgPdf.svg',
    'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartSvg.svg'
)
foreach ($a in $assets) { if (Test-Path $a) { Get-Item $a | Select-Object Name,Length } else { Write-Output "Missing: $a" } }
