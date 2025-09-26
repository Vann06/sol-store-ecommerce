$f = 'C:\Windows\Temp\export_check_fast_debug.pdf'
if (!(Test-Path $f)) { Write-Output "PDF not found: $f"; exit 1 }
$bytes = [System.IO.File]::ReadAllBytes($f)
$pngSig = [byte[]](137,80,78,71)
function IndexOfBytes($haystack, $needle) {
    for ($i=0; $i -le $haystack.Length - $needle.Length; $i++) {
        $match = $true
        for ($j=0; $j -lt $needle.Length; $j++) {
            if ($haystack[$i+$j] -ne $needle[$j]) { $match = $false; break }
        }
        if ($match) { return $i }
    }
    return -1
}
$idx = IndexOfBytes $bytes $pngSig
Write-Output "PDF Size: $((Get-Item $f).Length) bytes"
if ($idx -ge 0) { Write-Output "Found PNG signature at offset: $idx" } else { Write-Output "PNG signature not found" }
$asciis = [System.Text.Encoding]::ASCII.GetString($bytes)
if ($asciis.IndexOf('<svg') -ge 0) { Write-Output "Found literal '<svg' text in PDF" } else { Write-Output "No literal '<svg' found in PDF" }
