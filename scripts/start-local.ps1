param(
    [int]$Port = 8000,
    [string]$MysqlHome = 'C:\xampp\mysql'
)
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path $PSScriptRoot -Parent
$phpExe = Join-Path $projectRoot '.tools\php-8.5.11\php.exe'
$dataDir = Join-Path $projectRoot 'storage\local-mysql'
$logDir = Join-Path $projectRoot 'storage\logs'
New-Item -ItemType Directory -Force $logDir, (Join-Path $projectRoot 'storage\sessions') | Out-Null
if (!(Test-Path (Join-Path $projectRoot 'config\config.php'))) {
    Copy-Item -LiteralPath (Join-Path $projectRoot 'config\config.example.php') -Destination (Join-Path $projectRoot 'config\config.php')
}
if (!(Test-Path $phpExe)) { throw 'Bundled PHP missing. Extract the PHP 8.5 Windows ZIP into .tools\php-8.5.11.' }
if (!(Test-Path (Join-Path $projectRoot 'config\local.php'))) {
    Copy-Item -LiteralPath (Join-Path $projectRoot 'config\local.example.php') -Destination (Join-Path $projectRoot 'config\local.php')
}
$dbPidFile = Join-Path $logDir 'local-mysql.pid'
$webPidFile = Join-Path $logDir 'local-php.pid'
function Get-ManagedProcess($pidFile, $expectedExe) {
    if (Test-Path $pidFile) {
        $managedId = [int](Get-Content $pidFile -Raw).Trim()
        $process = Get-Process -Id $managedId -ErrorAction SilentlyContinue
        if ($process -and $process.Path -eq $expectedExe) { return $process }
    }
    return $null
}
$mysqldExe = Join-Path $MysqlHome 'bin\mysqld.exe'
function Test-LocalPort([int]$portNumber) {
    $client = [System.Net.Sockets.TcpClient]::new()
    try { return $client.ConnectAsync('127.0.0.1', $portNumber).Wait(500) -and $client.Connected }
    catch { return $false }
    finally { $client.Dispose() }
}
$freshDatabase = $false
if (!(Get-ManagedProcess $dbPidFile $mysqldExe)) {
    if (Test-LocalPort 3307) {
        throw 'Port 3307 is already in use by an unmanaged database. Stop it or use your own database configuration.'
    }
    if (!(Test-Path (Join-Path $dataDir 'mysql'))) {
        $templateDir = Join-Path $MysqlHome 'backup'
        if (!(Test-Path (Join-Path $templateDir 'mysql'))) { throw 'XAMPP clean system-table template missing at backup\mysql.' }
        New-Item -ItemType Directory -Force $dataDir | Out-Null
        # Copy the clean template's matching InnoDB files with its system tables.
        Get-ChildItem -LiteralPath $templateDir -Force | Copy-Item -Destination $dataDir -Recurse
        $freshDatabase = $true
    }
    $dbArgs = @('--no-defaults', "--basedir=`"$MysqlHome`"", "--datadir=`"$dataDir`"", '--port=3307', '--bind-address=127.0.0.1', '--skip-name-resolve', '--max-allowed-packet=16M', '--console')
    $dbProcess = Start-Process -FilePath $mysqldExe -ArgumentList $dbArgs -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $logDir 'local-mysql-stdout.log') -RedirectStandardError (Join-Path $logDir 'local-mysql.log')
    Set-Content -LiteralPath $dbPidFile -Value $dbProcess.Id
}
$dbReady = $false
for ($attempt = 0; $attempt -lt 30; $attempt++) {
    if ((Test-LocalPort 3307) -and (Select-String -LiteralPath (Join-Path $logDir 'local-mysql.log') -Pattern 'ready for connections' -Quiet)) { $dbReady = $true; break }
    Start-Sleep -Milliseconds 500
}
if (!$dbReady) { throw 'MariaDB did not start. Check storage\logs\local-mysql.log.' }
if ($freshDatabase) {
    & (Join-Path $MysqlHome 'bin\mysql.exe') --no-defaults --host=127.0.0.1 --port=3307 --user=root '--execute=ALTER USER ''root''@''127.0.0.1'' IDENTIFIED BY ''LocalDemoRoot2026!''; ALTER USER ''root''@''localhost'' IDENTIFIED BY ''LocalDemoRoot2026!''; ALTER USER ''root''@''::1'' IDENTIFIED BY ''LocalDemoRoot2026!'';'
    if ($LASTEXITCODE -ne 0) { throw 'Could not configure the local demo database administrator.' }
}
Push-Location (Join-Path $projectRoot 'public')
try {
    & $phpExe -c '../scripts/php-local.ini' '../scripts/setup-demo.php'
    if ($LASTEXITCODE -ne 0) { throw 'Demo setup failed.' }
    if (!(Get-ManagedProcess $webPidFile $phpExe)) {
        if (Test-LocalPort $Port) { throw "Port $Port is already in use." }
        $webProcess = Start-Process -FilePath $phpExe -ArgumentList '-c','../scripts/php-local.ini','-S',"127.0.0.1:$Port",'dev-router.php' -WorkingDirectory (Join-Path $projectRoot 'public') -WindowStyle Hidden -PassThru -RedirectStandardOutput (Join-Path $logDir 'local-web.log') -RedirectStandardError (Join-Path $logDir 'local-web-error.log')
        Set-Content -LiteralPath $webPidFile -Value $webProcess.Id
    }
} finally { Pop-Location }
Write-Host "Local app: http://127.0.0.1:$Port"
Write-Host 'Demo login: admin@example.test / DemoDental2026!'
