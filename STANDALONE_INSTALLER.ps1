# ========================================
# Mobile Shop POS - Standalone Installer
# Single file that downloads and installs everything
# No other files needed!
# ========================================

# Check for admin rights
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "========================================" -ForegroundColor Red
    Write-Host "Administrator Rights Required!" -ForegroundColor Red
    Write-Host "========================================" -ForegroundColor Red
    Write-Host ""
    Write-Host "This installer needs administrator rights." -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Please:" -ForegroundColor Yellow
    Write-Host "1. Right-click this file" -ForegroundColor White
    Write-Host "2. Select 'Run with PowerShell'" -ForegroundColor White
    Write-Host "3. Click 'Yes' on UAC prompt" -ForegroundColor White
    Write-Host ""
    Write-Host "Press any key to exit..." -ForegroundColor Yellow
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    exit 1
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Mobile Shop POS - Standalone Installer" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "This installer will:" -ForegroundColor Yellow
Write-Host "  1. Download XAMPP (if needed)" -ForegroundColor White
Write-Host "  2. Install XAMPP silently" -ForegroundColor White
Write-Host "  3. Download Mobile Shop POS" -ForegroundColor White
Write-Host "  4. Setup database" -ForegroundColor White
Write-Host "  5. Configure application" -ForegroundColor White
Write-Host "  6. Open in browser" -ForegroundColor White
Write-Host ""
Write-Host "Total time: 10-20 minutes" -ForegroundColor Yellow
Write-Host "Internet required: Yes" -ForegroundColor Yellow
Write-Host ""
Write-Host "Continue? (Y/N): " -ForegroundColor Yellow -NoNewline
$response = Read-Host

if ($response -ne 'Y' -and $response -ne 'y') {
    Write-Host ""
    Write-Host "Installation cancelled." -ForegroundColor Red
    Write-Host ""
    Write-Host "Press any key to exit..." -ForegroundColor Yellow
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    exit 0
}

# Configuration
$xamppVersion = "8.2.12"
$xamppUrl = "https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/$xamppVersion/xampp-windows-x64-$xamppVersion-0-VS16-installer.exe/download"
$xamppInstaller = "$env:TEMP\xampp-installer.exe"
$xamppPath = "C:\xampp"

# Application configuration
$appName = "mobile-shop-pos"
$appVersion = "v2.0.0"
$appZipUrl = "https://github.com/mbrown1837/Mobile-Shop-POS/releases/download/$appVersion/mobile-shop-pos-$appVersion.zip"
$appZipFile = "$env:TEMP\mobile-shop-pos.zip"
$tempExtractPath = "$env:TEMP\mobile-shop-pos-extract"
$destPath = "$xamppPath\htdocs\$appName"

# Database configuration
$dbName = "mobile_shop_pos"
$dbUser = "root"
$dbPass = ""
$dbFile = "$destPath\database\mobile_shop_pos_v2.0.0_clean.sql"

# Helper functions
function Write-Step($step, $total, $message) {
    Clear-Host
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "Mobile Shop POS - Installation Progress" -ForegroundColor Cyan
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Step $step/$total : $message" -ForegroundColor Yellow
    Write-Host ""
}

function Write-Success($message) {
    Write-Host "[OK] $message" -ForegroundColor Green
}

function Write-Info($message) {
    Write-Host "[!] $message" -ForegroundColor Yellow
}

function Write-Error2($message) {
    Write-Host "[ERROR] $message" -ForegroundColor Red
}

# ========================================
# Step 1: Check XAMPP
# ========================================
Write-Step 1 6 "Checking XAMPP Installation"

$xamppInstalled = Test-Path "$xamppPath\xampp-control.exe"

if ($xamppInstalled) {
    Write-Success "XAMPP found at $xamppPath"
} else {
    Write-Info "XAMPP not found. Will download and install."
    Write-Host ""
    
    # Download XAMPP
    Write-Host "Downloading XAMPP $xamppVersion..." -ForegroundColor Yellow
    Write-Host "Size: ~150 MB - This may take 5-10 minutes..." -ForegroundColor Yellow
    Write-Host ""
    
    try {
        # Clean up potential corrupted file
        if (Test-Path $xamppInstaller) {
            try {
                Remove-Item $xamppInstaller -Force -ErrorAction Stop
            } catch {
                Write-Error2 "Could not remove existing installer file."
                Write-Host "The file '$xamppInstaller' might be in use or corrupted." -ForegroundColor Red
                Write-Host "Please manually delete this file and try again." -ForegroundColor Yellow
                pause
                exit 1
            }
        }

        # Download with BITS (more reliable)
        Write-Host "Downloading using BITS..." -ForegroundColor Yellow
        try {
            Import-Module BitsTransfer -ErrorAction SilentlyContinue
            Start-BitsTransfer -Source $xamppUrl -Destination $xamppInstaller -Priority Foreground -ErrorAction Stop
        } catch {
            Write-Host "BITS transfer failed, falling back to WebRequest..." -ForegroundColor Yellow
            $ProgressPreference = 'SilentlyContinue'
            Invoke-WebRequest -Uri $xamppUrl -OutFile $xamppInstaller -UseBasicParsing
            $ProgressPreference = 'Continue'
        }
        
        # Verify download
        if (Test-Path $xamppInstaller) {
            $fileSize = (Get-Item $xamppInstaller).Length
            if ($fileSize -lt 100000000) { # Less than 100MB
                throw "Downloaded file is too small ($([math]::Round($fileSize/1MB, 2)) MB). Likely a broken download."
            }
            Write-Success "XAMPP downloaded ($([math]::Round($fileSize/1MB, 2)) MB)"
        } else {
            throw "Download failed - File not found"
        }
        Write-Host ""
        
        # Install XAMPP
        Write-Host "Installing XAMPP silently..." -ForegroundColor Yellow
        Write-Host "This will take 5-10 minutes..." -ForegroundColor Yellow
        Write-Host ""
        
        # Disable unused components to improve reliability and speed
        # We need: Apache, MySQL, PHP, PHPMyAdmin
        # We disable: FileZilla, Mercury, Tomcat, Perl, Webalizer, Sendmail
        $installArgs = "--mode unattended --launchapps 0 --disable-components xampp_filezilla,xampp_mercury,xampp_tomcat,xampp_perl,xampp_webalizer,xampp_sendmail"
        $process = Start-Process -FilePath $xamppInstaller -ArgumentList $installArgs -Wait -NoNewWindow -PassThru
        
        Start-Sleep -Seconds 10
        
        if (Test-Path "$xamppPath\xampp-control.exe") {
            Write-Success "XAMPP installed successfully"
            Remove-Item $xamppInstaller -Force -ErrorAction SilentlyContinue
        } else {
            Write-Error2 "XAMPP silent installation failed"
            if ($process.ExitCode) { Write-Host "Installer Exit Code: $($process.ExitCode)" -ForegroundColor Red }
            
            Write-Host ""
            Write-Host "Possible reasons:" -ForegroundColor Yellow
            Write-Host "1. Antivirus blocked the silent install" -ForegroundColor White
            Write-Host "2. C:\xampp already exists and is not empty" -ForegroundColor White
            Write-Host ""
            Write-Host "Would you like to try installing XAMPP interactively?" -ForegroundColor Yellow
            Write-Host "This will open the setup window so you can see any errors." -ForegroundColor White
            Write-Host "Press 'Y' to try interactive install, or 'N' to exit: " -NoNewline -ForegroundColor Yellow
            $retry = Read-Host
            
            if ($retry -eq 'Y' -or $retry -eq 'y') {
                 Write-Host ""
                 Write-Host "Launching installer..." -ForegroundColor Yellow
                 Write-Host "IMPORTANT: Install to C:\xampp (default)" -ForegroundColor Cyan
                 Start-Process -FilePath $xamppInstaller -Wait
                 
                 if (Test-Path "$xamppPath\xampp-control.exe") {
                    Write-Success "XAMPP installed successfully"
                    Remove-Item $xamppInstaller -Force -ErrorAction SilentlyContinue
                 } else {
                    Write-Error2 "XAMPP installation still failed."
                    Write-Host "Please install XAMPP manually to C:\xampp and run this script again." -ForegroundColor Yellow
                    pause
                    exit 1
                 }
            } else {
                exit 1
            }
        }
    } catch {
        Write-Error2 "Failed to download/install XAMPP"
        Write-Host ""
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host ""
        Write-Host "Press any key to exit..." -ForegroundColor Yellow
        $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
        exit 1
    }
}

Write-Host ""
Start-Sleep -Seconds 2

# ========================================
# Step 2: Start Services
# ========================================
Write-Step 2 6 "Starting XAMPP Services"

Write-Host "Starting Apache..." -ForegroundColor Yellow
Start-Process "$xamppPath\apache_start.bat" -WindowStyle Hidden -ErrorAction SilentlyContinue
Write-Success "Apache started"

Write-Host "Starting MySQL..." -ForegroundColor Yellow
Start-Process "$xamppPath\mysql_start.bat" -WindowStyle Hidden -ErrorAction SilentlyContinue
Write-Success "MySQL started"

Write-Host ""
Write-Host "Waiting for services..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# ========================================
# Step 3: Download Application
# ========================================
Write-Step 3 6 "Downloading Mobile Shop POS"

Write-Host "Downloading application package..." -ForegroundColor Yellow
Write-Host "Size: ~5 MB" -ForegroundColor Yellow
Write-Host ""

try {
    $ProgressPreference = 'SilentlyContinue'
    Invoke-WebRequest -Uri $appZipUrl -OutFile $appZipFile -UseBasicParsing
    $ProgressPreference = 'Continue'
    
    Write-Success "Application downloaded"
    Write-Host ""
    
    # Extract
    Write-Host "Extracting files..." -ForegroundColor Yellow
    if (Test-Path $tempExtractPath) {
        Remove-Item $tempExtractPath -Recurse -Force
    }
    Expand-Archive -Path $appZipFile -DestinationPath $tempExtractPath -Force
    
    Write-Success "Files extracted"
    
} catch {
    Write-Error2 "Failed to download application"
    Write-Host ""
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please check:" -ForegroundColor Yellow
    Write-Host "- Internet connection" -ForegroundColor White
    Write-Host "- GitHub release URL is correct" -ForegroundColor White
    Write-Host ""
    Write-Host "Press any key to exit..." -ForegroundColor Yellow
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    exit 1
}

Write-Host ""
Start-Sleep -Seconds 2

# ========================================
# Step 4: Install Application
# ========================================
Write-Step 4 6 "Installing Application"

# Find extracted folder
$extractedFolder = Get-ChildItem -Path $tempExtractPath -Directory | Select-Object -First 1
if ($extractedFolder) {
    $sourcePath = $extractedFolder.FullName
} else {
    $sourcePath = $tempExtractPath
}

Write-Host "Copying files to htdocs..." -ForegroundColor Yellow

# Create destination
if (-not (Test-Path $destPath)) {
    New-Item -ItemType Directory -Path $destPath -Force | Out-Null
}

# Copy all files
Get-ChildItem -Path $sourcePath | ForEach-Object {
    Copy-Item -Path $_.FullName -Destination $destPath -Recurse -Force -ErrorAction SilentlyContinue
}

Write-Success "Application installed"

# Cleanup
Remove-Item $appZipFile -Force -ErrorAction SilentlyContinue
Remove-Item $tempExtractPath -Recurse -Force -ErrorAction SilentlyContinue

Write-Host ""
Start-Sleep -Seconds 2

# ========================================
# Step 5: Setup Database
# ========================================
Write-Step 5 6 "Setting Up Database"

$mysqlBin = "$xamppPath\mysql\bin\mysql.exe"

Write-Host "Creating database..." -ForegroundColor Yellow
try {
    $createDbCmd = "CREATE DATABASE IF NOT EXISTS $dbName;"
    & $mysqlBin -u $dbUser -e $createDbCmd 2>$null
    Write-Success "Database created: $dbName"
} catch {
    Write-Error2 "Failed to create database"
    pause
    exit 1
}

Write-Host "Importing database..." -ForegroundColor Yellow
if (Test-Path $dbFile) {
    try {
        Get-Content $dbFile | & $mysqlBin -u $dbUser $dbName 2>$null
        Write-Success "Database imported"
    } catch {
        Write-Error2 "Failed to import database"
        pause
        exit 1
    }
} else {
    Write-Error2 "Database file not found"
    pause
    exit 1
}

Write-Host ""
Start-Sleep -Seconds 2

# ========================================
# Step 6: Configure Application
# ========================================
Write-Step 6 6 "Configuring Application"

$configDb = "$destPath\application\config\database.php"
$configBase = "$destPath\application\config\config.php"

Write-Host "Configuring database..." -ForegroundColor Yellow
(Get-Content $configDb) -replace "'hostname' => '.*?'", "'hostname' => 'localhost'" | Set-Content $configDb
(Get-Content $configDb) -replace "'username' => '.*?'", "'username' => 'root'" | Set-Content $configDb
(Get-Content $configDb) -replace "'password' => '.*?'", "'password' => ''" | Set-Content $configDb
(Get-Content $configDb) -replace "'database' => '.*?'", "'database' => 'mobile_shop_pos'" | Set-Content $configDb
Write-Success "Database configured"

Write-Host "Configuring base URL..." -ForegroundColor Yellow
(Get-Content $configBase) -replace "\`$config\['base_url'\] = '.*?';", "`$config['base_url'] = 'http://localhost/$appName/';" | Set-Content $configBase
Write-Success "Base URL configured"

Write-Host "Creating installation flag..." -ForegroundColor Yellow
Get-Date | Out-File "$destPath\application\config\installed.txt"
Write-Success "Installation complete"

Write-Host ""
Start-Sleep -Seconds 2

# ========================================
# Complete!
# ========================================
Clear-Host
Write-Host "========================================" -ForegroundColor Green
Write-Host "Installation Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "[SUCCESS] Mobile Shop POS is ready!" -ForegroundColor Green
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Installation Details:" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Location: $destPath" -ForegroundColor White
Write-Host "Database: $dbName" -ForegroundColor White
Write-Host "URL: http://localhost/$appName/" -ForegroundColor White
Write-Host ""
Write-Host "Default Login:" -ForegroundColor Yellow
Write-Host "  Username: admin" -ForegroundColor White
Write-Host "  Password: admin123" -ForegroundColor White
Write-Host ""
Write-Host "[!] IMPORTANT: Change password after login!" -ForegroundColor Red
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Opening in browser..." -ForegroundColor Yellow
Start-Sleep -Seconds 3

Start-Process "http://localhost/$appName/"

Write-Host ""
Write-Host "Installation complete! Enjoy using Mobile Shop POS!" -ForegroundColor Green
Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
