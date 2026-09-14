$ErrorActionPreference = 'Continue'
$ssh    = "C:\Windows\System32\OpenSSH\ssh.exe"
$key    = "$env:USERPROFILE\.ssh\cabrain_tunnel"
$log    = "E:\Sites\tomatophp\storage\logs\tunnel.log"
# Nginx Proxy Manager host #59 (tomato.fadymondy.com + tomatophp.fadymondy.com) forwards to
# 10.10.10.1:4573 on the Proxmox host (4491 and 4484 are taken there).
$remote = "10.10.10.1:4573"
$local  = "127.0.0.1:8010"

"[$(Get-Date -Format s)] wrapper starting: $remote -> $local" | Add-Content $log

while ($true) {
    "[$(Get-Date -Format s)] connecting..." | Add-Content $log
    & $ssh -N -i $key `
        -o BatchMode=yes `
        -o ExitOnForwardFailure=yes `
        -o ServerAliveInterval=30 -o ServerAliveCountMax=3 `
        -o StrictHostKeyChecking=accept-new `
        -R "${remote}:${local}" root@100.70.229.51 2>&1 | Add-Content $log
    "[$(Get-Date -Format s)] ssh exited (code $LASTEXITCODE); retrying in 5s" | Add-Content $log
    Start-Sleep -Seconds 5
}
