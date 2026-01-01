# Mobile Shop POS - Release ZIP Creator
# Version 1.0.0
# Creates a clean distribution package

$version = "v1.0.0"
$projectName = "mobile-shop-pos"
$zipName = "$projectName-$version.zip"
$tempFolder = "$projectName-$version"

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Mobile Shop POS - Release Packager" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "Creating release package: $zipName" -ForegroundColor Green
Write-Host "Please wait...`n" -ForegroundColor Yellow

# Create temp folder
if (Test-Path $tempFolder) {
    Write-Host "Removing old temp folder..." -ForegroundColor Yellow
    Remove-Item $tempFolder -Recurse -Force
}
New-Item -ItemType Directory -Path $tempFolder | Out-Null

# Files and folders to EXCLUDE
$excludePatterns = @(
    '*.git*',
    '*\.vscode*',
    '*\.idea*',
    '*.sublime-*',
    '*\.DS_Store*',
    '*Thumbs.db*',
    '*desktop.ini*',
    '*.tmp',
    '*.bak',
    '*.swp',
    '*~',
    '*create-release-zip.ps1*',
    "*$zipName*",
    '*application\cache\*.php',
    '*application\logs\*.log',
    '*application\logs\log-*.php'
)

# Copy all files
Write-Host "Copying project files..." -ForegroundColor Yellow
$fileCount = 0

Get-ChildItem -Path . -Recurse -Force | ForEach-Object {
    $relativePath = $_.FullName.Substring((Get-Location).Path.Length + 1)
    
    # Check if should exclude
    $shouldExclude = $false
    foreach ($pattern in $excludePatterns) {
        if ($relativePath -like $pattern) {
            $shouldExclude = $true
            break
        }
    }
    
    # Special handling for cache and logs folders
    if ($relativePath -like '*application\cache\*' -and $_.Name -ne 'index.html' -and $_.Name -ne '.htaccess') {
        $shouldExclude = $true
    }
    if ($relativePath -like '*application\logs\*' -and $_.Name -ne 'index.html' -and $_.Name -ne '.htaccess') {
        $shouldExclude = $true
    }
    
    if (-not $shouldExclude) {
        $destination = Join-Path $tempFolder $relativePath
        
        if ($_.PSIsContainer) {
            if (-not (Test-Path $destination)) {
                New-Item -ItemType Directory -Path $destination -Force | Out-Null
            }
        } else {
            $destDir = Split-Path $destination -Parent
            if (-not (Test-Path $destDir)) {
                New-Item -ItemType Directory -Path $destDir -Force | Out-Null
            }
            Copy-Item $_.FullName -Destination $destination -Force
            $fileCount++
            
            if ($fileCount % 100 -eq 0) {
                Write-Host "  Copied $fileCount files..." -ForegroundColor Gray
            }
        }
    }
}

Write-Host "  Total files copied: $fileCount" -ForegroundColor Green

# Ensure important folders exist with index.html
Write-Host "`nEnsuring folder structure..." -ForegroundColor Yellow

$keepFolders = @(
    "application\cache",
    "application\logs"
)

foreach ($folder in $keepFolders) {
    $folderPath = Join-Path $tempFolder $folder
    if (-not (Test-Path $folderPath)) {
        New-Item -ItemType Directory -Path $folderPath -Force | Out-Null
    }
    
    # Create index.html if not exists
    $indexPath = Join-Path $folderPath "index.html"
    if (-not (Test-Path $indexPath)) {
        @"
<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
</head>
<body>
    <h1>Directory access is forbidden.</h1>
</body>
</html>
"@ | Out-File -FilePath $indexPath -Encoding UTF8
        Write-Host "  Created: $folder\index.html" -ForegroundColor Gray
    }
}

# Create ZIP
Write-Host "`nCreating ZIP archive..." -ForegroundColor Yellow
if (Test-Path $zipName) {
    Remove-Item $zipName -Force
}

try {
    Compress-Archive -Path $tempFolder -DestinationPath $zipName -CompressionLevel Optimal
    
    # Cleanup temp folder
    Remove-Item $tempFolder -Recurse -Force
    
    # Show result
    $zipSize = (Get-Item $zipName).Length / 1MB
    
    Write-Host "`n========================================" -ForegroundColor Green
    Write-Host "  SUCCESS!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "`nPackage created: $zipName" -ForegroundColor Cyan
    Write-Host "Size: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Cyan
    Write-Host "Files: $fileCount" -ForegroundColor Cyan
    Write-Host "`nReady to upload to GitHub Release!" -ForegroundColor Green
    Write-Host "`nNext steps:" -ForegroundColor Yellow
    Write-Host "1. Go to: https://github.com/YOUR_USERNAME/mobile-shop-pos/releases" -ForegroundColor White
    Write-Host "2. Click 'Create release from tag' for v1.0.0" -ForegroundColor White
    Write-Host "3. Attach this ZIP file" -ForegroundColor White
    Write-Host "4. Publish release`n" -ForegroundColor White
    
} catch {
    Write-Host "`nERROR: Failed to create ZIP file" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    
    # Cleanup on error
    if (Test-Path $tempFolder) {
        Remove-Item $tempFolder -Recurse -Force
    }
}
