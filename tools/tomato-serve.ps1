$ErrorActionPreference = 'Continue'
$php  = (Get-Command php).Source
$root = 'E:\Sites\tomatophp'
$log  = "$root\storage\logs\serve.log"

Set-Location $root
"[$(Get-Date -Format s)] wrapper starting: php artisan serve on 127.0.0.1:8010" | Add-Content $log

while ($true) {
    & $php artisan serve --host=127.0.0.1 --port=8010 --no-reload 2>&1 | Add-Content $log
    "[$(Get-Date -Format s)] server exited (code $LASTEXITCODE); restarting in 5s" | Add-Content $log
    Start-Sleep -Seconds 5
}
