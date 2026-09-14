$ErrorActionPreference = 'Continue'
$php  = (Get-Command php).Source
$root = 'E:\Sites\tomatophp'
$log  = "$root\storage\logs\schedule.log"

# Runs the Laravel scheduler for the public demo (hourly migrate:fresh --seed when DEMO_MODE=true).
Set-Location $root
"[$(Get-Date -Format s)] wrapper starting: php artisan schedule:work" | Add-Content $log

while ($true) {
    & $php artisan schedule:work 2>&1 | Add-Content $log
    "[$(Get-Date -Format s)] scheduler exited (code $LASTEXITCODE); restarting in 5s" | Add-Content $log
    Start-Sleep -Seconds 5
}
