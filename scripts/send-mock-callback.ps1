# Sends a sample STK callback JSON to the local callback endpoint (Docker maps 8080 -> 80).
param(
    [string]$BaseUrl = "http://localhost:8080"
)

$ErrorActionPreference = "Stop"
$uri = "$BaseUrl/callback.php"
$json = Get-Content -Path "$PSScriptRoot\stk-callback-sample.json" -Raw

Write-Host "POST $uri"
$response = Invoke-RestMethod -Uri $uri -Method Post -Body $json -ContentType "application/json; charset=utf-8"
$response | ConvertTo-Json -Compress
Write-Host "Done. Check storage/logs/mpesa_callback.log inside the project."
