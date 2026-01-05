# Mobile Shop POS - Release Package Creator
# This script creates a clean release package for distribution

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Mobile Shop POS - Release Package Creator" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$version = "v2.0.0"
$releaseFolder = "mobile-shop-pos-$version"
$zipFile = "mobile-shop-pos-$version.zip"

# Files and folders to include
$includeItems = @(
    "application",
    "system",
    "public",
    "database/mobile_shop_pos_v2.0.0_final.sql",
    "index.php",
    "install.php",
    "ONE_CLICK_INSTALLER.bat",
    "FULL_AUTO_INSTALLER.ps1",
    ".htaccess",
    "composer.json",
    "license.txt",
    "README.md",
    "INSTALLATION_GUIDE.md",
    "QUICK_SETUP.md",
    "AUTOMATED_INSTALLER_GUIDE.md",
    "ONE_CLICK_INSTALLATION_GUIDE.md",
    "INSTALLATION_OPTIONS_SUMMARY.md",
    "SYSTEM_VERIFICATION_CHECKLIST.md"
)

# Create release folder
Write-Host "Creating release folder..." -ForegroundColor Yellow
if (Test-Path $releaseFolder) {
    Remove-Item -Recurse -Force $releaseFolder
}
New-Item -ItemType Directory -Path $releaseFolder | Out-Null

# Copy files
Write-Host "Copying files..." -ForegroundColor Yellow
foreach ($item in $includeItems) {
    if (Test-Path $item) {
        $destination = Join-Path $releaseFolder $item
        $destinationDir = Split-Path $destination -Parent
        
        if (-not (Test-Path $destinationDir)) {
            New-Item -ItemType Directory -Path $destinationDir -Force | Out-Null
        }
        
        if (Test-Path $item -PathType Container) {
            Copy-Item -Path $item -Destination $destination -Recurse -Force
            Write-Host "  + Copied folder: $item" -ForegroundColor Green
        } else {
            Copy-Item -Path $item -Destination $destination -Force
            Write-Host "  + Copied file: $item" -ForegroundColor Green
        }
    } else {
        Write-Host "  - Not found: $item" -ForegroundColor Red
    }
}

# Clean up unnecessary files from release
Write-Host ""
Write-Host "Cleaning up..." -ForegroundColor Yellow

$cleanupPaths = @(
    "$releaseFolder/application/cache/*",
    "$releaseFolder/application/logs/*",
    "$releaseFolder/.env"
)

foreach ($path in $cleanupPaths) {
    if (Test-Path $path) {
        Remove-Item -Path $path -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "  + Cleaned: $path" -ForegroundColor Green
    }
}

# Create .gitkeep files for empty directories
$emptyDirs = @(
    "$releaseFolder/application/cache",
    "$releaseFolder/application/logs"
)

foreach ($dir in $emptyDirs) {
    if (Test-Path $dir) {
        New-Item -Path "$dir/.gitkeep" -ItemType File -Force | Out-Null
    }
}

# Create ZIP file
Write-Host ""
Write-Host "Creating ZIP file..." -ForegroundColor Yellow

if (Test-Path $zipFile) {
    Remove-Item $zipFile -Force
}

Compress-Archive -Path $releaseFolder -DestinationPath $zipFile -CompressionLevel Optimal

# Get file size
$zipSize = (Get-Item $zipFile).Length / 1MB
$zipSizeFormatted = "{0:N2} MB" -f $zipSize

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SUCCESS! Release package created" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Release Folder: $releaseFolder" -ForegroundColor White
Write-Host "ZIP File: $zipFile" -ForegroundColor White
Write-Host "File Size: $zipSizeFormatted" -ForegroundColor White
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "1. Test the release package" -ForegroundColor White
Write-Host "2. Extract and install on fresh system" -ForegroundColor White
Write-Host "3. Verify all features work" -ForegroundColor White
Write-Host "4. Distribute to users" -ForegroundColor White
Write-Host ""
Write-Host "Ready for distribution!" -ForegroundColor Green
