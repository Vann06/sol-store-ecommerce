$pdfPath = 'C:\Windows\Temp\export_check_fast_debug.pdf'
if (-Not (Test-Path $pdfPath)) {
    Write-Output "PDF not found at $pdfPath"
    exit 1
}

# Read raw bytes and do an ASCII marker search (PDF objects are ASCII headers + binary streams)
[byte[]]$bytes = [System.IO.File]::ReadAllBytes($pdfPath)
$txt = [System.Text.Encoding]::ASCII.GetString($bytes)

$markers = @(
    '/Subtype /Image',
    '/XObject',
    '/Filter /DCTDecode',
    '/Filter /FlateDecode',
    '/Filter /JPXDecode',
    '/ColorSpace',
    '/Width',
    '/Height',
    '/BitsPerComponent',
    '/Resources'
)

foreach ($m in $markers) {
    if ($txt -match [regex]::Escape($m)) {
        Write-Output "Marker found: $m"
    } else {
        Write-Output "Marker NOT found: $m"
    }
}

# Find 'stream' occurrences (image/object streams) and show small contexts
$streamMatches = [regex]::Matches($txt,'\bstream\b')
if ($streamMatches.Count -eq 0) {
    Write-Output "No 'stream' keywords found"
} else {
    Write-Output "Found 'stream' occurrences: $($streamMatches.Count) - showing offsets and small context:"
    foreach ($match in $streamMatches) {
        $s = $match.Index
        $start = [Math]::Max(0,$s-80)
        $len = [Math]::Min(200, $txt.Length - $start)
        $context = $txt.Substring($start,$len)
        Write-Output ([string]::Format("offset {0}: ...{1}...", $s, $context))
    }
}
