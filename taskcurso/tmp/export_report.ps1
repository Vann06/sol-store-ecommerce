$session = New-Object Microsoft.PowerShell.Commands.WebRequestSession
# GET the filtros page to initialize cookies/session
Invoke-WebRequest -Uri 'http://localhost:8000/admin/reports/filtros/fechas?fecha_inicio=2025-09-01&fecha_fin=2025-09-30' -WebSession $session -OutFile 'C:\Windows\Temp\filtros_get.html' -UseBasicParsing

# POST to exportar/pdf with form fields; include debug_dump and fast
$body = @{
    tipo = 'ventas'
    fecha_inicio = '2025-09-01'
    fecha_fin = '2025-09-30'
    fast = 'true'
    debug_dump = 'true'
}

Invoke-WebRequest -Uri 'http://localhost:8000/admin/reports/exportar/pdf' -Method Post -Body $body -WebSession $session -OutFile 'C:\Windows\Temp\export_check_fast_debug.pdf' -UseBasicParsing

Write-Output "Saved files:" 
if (Test-Path 'C:\Windows\Temp\export_check_fast_debug.pdf') { Get-Item 'C:\Windows\Temp\export_check_fast_debug.pdf' | Select-Object Name,Length }
if (Test-Path 'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartSvgPdf.svg') { Write-Output 'Found debug SVG'; Get-Item 'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartSvgPdf.svg' | Select-Object Name,Length } else { Write-Output 'No debug SVG' }
if (Test-Path 'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartPng.png') { Write-Output 'Found debug PNG'; Get-Item 'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartPng.png' | Select-Object Name,Length } else { Write-Output 'No debug PNG' }
if (Test-Path 'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartSvg.svg') { Write-Output 'Found raw SVG'; Get-Item 'c:\Users\paula\Downloads\nginx-1.27.3\html\sol-store-ecommerce\taskcurso\storage\logs\report_chartSvg.svg' | Select-Object Name,Length } else { Write-Output 'No raw SVG' }