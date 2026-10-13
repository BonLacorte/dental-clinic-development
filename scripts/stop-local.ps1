param([string]$MysqlHome = 'C:\xampp\mysql')
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path $PSScriptRoot -Parent
$logDir = Join-Path $projectRoot 'storage\logs'
$webPidFile = Join-Path $logDir 'local-php.pid'
if (Test-Path $webPidFile) {
    $managedId = [int](Get-Content $webPidFile -Raw).Trim()
    $process = Get-Process -Id $managedId -ErrorAction SilentlyContinue
    if ($process -and $process.Path -eq (Join-Path $projectRoot '.tools\php-8.5.11\php.exe')) {
        Stop-Process -Id $managedId
        if (!$process.WaitForExit(15000)) { throw 'PHP has not finished stopping.' }
    }
    Remove-Item -LiteralPath $webPidFile
}
$dbPidFile = Join-Path $logDir 'local-mysql.pid'
if (Test-Path $dbPidFile) {
    $managedId = [int](Get-Content $dbPidFile -Raw).Trim()
    $process = Get-Process -Id $managedId -ErrorAction SilentlyContinue
    if ($process -and $process.Path -eq (Join-Path $MysqlHome 'bin\mysqld.exe')) {
        $previousMysqlPassword = $env:MYSQL_PWD
        $env:MYSQL_PWD = 'LocalDemoRoot2026!'
        try {
            & (Join-Path $MysqlHome 'bin\mysqladmin.exe') --no-defaults --host=127.0.0.1 --port=3307 --user=root shutdown
            if ($LASTEXITCODE -ne 0) { throw 'MariaDB shutdown failed.' }
            if (!$process.WaitForExit(15000)) { throw 'MariaDB has not finished shutting down.' }
        } finally {
            if ($null -eq $previousMysqlPassword) { Remove-Item Env:MYSQL_PWD }
            else { $env:MYSQL_PWD = $previousMysqlPassword }
        }
    }
    Remove-Item -LiteralPath $dbPidFile
}
Write-Host 'Local app and database stopped. Demo data is preserved.'
