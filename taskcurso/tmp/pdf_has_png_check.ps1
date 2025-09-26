$f = 'C:\Windows\Temp\export_check_fast_debug.pdf'
if (!(Test-Path $f)) { Write-Output "PDF not found: $f"; exit 1 }
$bytes = [System.IO.File]::ReadAllBytes($f)
$needle = @(137,80,78,71)
for ($i=0; $i -le $bytes.Length - $needle.Length; $i++) {
    $match = $true
    for ($j=0; $j -lt $needle.Length; $j++) {
        if ($bytes[$i+$j] -ne $needle[$j]) { $match = $false; break }
    }
    if ($match) { Write-Output "PNG signature found at offset $i"; exit 0 }
}
Write-Output "PNG signature not found in PDF"
exit 2
