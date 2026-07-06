$content = Get-Content 'azure\startup.sh' -Raw
$content = $content -replace "`r`n", "`n"
[System.IO.File]::WriteAllText("$(Get-Location)\azure\startup.sh", $content)

$content2 = Get-Content 'azure\nginx.conf' -Raw
$content2 = $content2 -replace "`r`n", "`n"
[System.IO.File]::WriteAllText("$(Get-Location)\azure\nginx.conf", $content2)

git add .gitattributes azure/startup.sh azure/nginx.conf
git update-index --chmod=+x azure/startup.sh azure/deploy.sh
.\azure\zip_deploy.ps1
