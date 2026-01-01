# 📦 Create Release ZIP Package

Complete guide to create a clean release ZIP file for GitHub.

---

## Method 1: PowerShell Script (Recommended - Automatic)

### Step 1: Create ZIP Script

Save this as `create-release-zip.ps1`:

```powershell
# Mobile Shop POS - Release ZIP Creator
# Version 1.0.0

$version = "v1.0.0"
$projectName = "mobile-shop-pos"
$zipName = "$projectName-$version.zip"
$tempFolder = "$projectName-$version"

Write-Host "Creating release package: $zipName" -ForegroundColor Green

# Create temp folder
if (Test-Path $tempFolder) {
    Remove-Item $tempFolder -Recurse -Force
}
New-Item -ItemType Directory -Path $tempFolder | Out-Null

# Files and folders to EXCLUDE
$exclude = @(
    '.git',
    '.gitignore',
    'application\cache\*',
    'application\logs\*.log',
    'application\logs\*.php',
    '.vscode',
    '.idea',
    '*.sublime-*',
    '.DS_Store',
    'Thumbs.db',
    'desktop.ini',
    '*.tmp',
    '*.bak',
    '*.swp',
    '*~',
    'create-release-zip.ps1',
    "$zipName"
)

# Copy all files except excluded
Write-Host "Copying files..." -ForegroundColor Yellow
Get-ChildItem -Path . -Recurse | ForEach-Object {
    $relativePath = $_.FullName.Substring((Get-Location).Path.Length + 1)
    
    $shouldExclude = $false
    foreach ($pattern in $exclude) {
        if ($relativePath -like $pattern) {
            $shouldExclude = $true
            break
        }
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
        }
    }
}

# Keep important empty folders with index.html
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
        "<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Directory access is forbidden.</h1></body></html>" | Out-File -FilePath $indexPath -Encoding UTF8
    }
}

# Create ZIP
Write-Host "Creating ZIP file..." -ForegroundColor Yellow
if (Test-Path $zipName) {
    Remove-Item $zipName -Force
}

Compress-Archive -Path $tempFolder -DestinationPath $zipName -CompressionLevel Optimal

# Cleanup temp folder
Remove-Item $tempFolder -Recurse -Force

# Show result
$zipSize = (Get-Item $zipName).Length / 1MB
Write-Host "`nSuccess! Created: $zipName" -ForegroundColor Green
Write-Host "Size: $([math]::Round($zipSize, 2)) MB" -ForegroundColor Cyan
Write-Host "`nReady to upload to GitHub Release!" -ForegroundColor Green
```

### Step 2: Run Script

```powershell
# Open PowerShell in project folder
cd C:\xampp\htdocs\mobile-shop-pos

# Run script
powershell -ExecutionPolicy Bypass -File create-release-zip.ps1
```

---

## Method 2: Manual ZIP Creation (If script doesn't work)

### Step 1: Create Clean Copy

```powershell
# Create release folder
mkdir mobile-shop-pos-v1.0.0

# Copy everything
xcopy /E /I . mobile-shop-pos-v1.0.0
```

### Step 2: Delete Unnecessary Files

Go to `mobile-shop-pos-v1.0.0` folder and delete:

**Folders to DELETE:**
- `.git` (entire folder)
- `.vscode` (if exists)
- `.idea` (if exists)
- `application/cache/*` (keep folder, delete contents except index.html)
- `application/logs/*.log` (delete log files, keep index.html)

**Files to DELETE:**
- `.gitignore`
- `create-release-zip.ps1`
- Any `.tmp`, `.bak`, `.swp` files
- `Thumbs.db`, `.DS_Store`, `desktop.ini`

### Step 3: Create ZIP

1. Right-click on `mobile-shop-pos-v1.0.0` folder
2. Select **"Send to" → "Compressed (zipped) folder"**
3. Rename to: `mobile-shop-pos-v1.0.0.zip`

### Step 4: Cleanup

Delete the `mobile-shop-pos-v1.0.0` folder (keep only ZIP)

---

## Method 3: Using 7-Zip (Best Compression)

### If you have 7-Zip installed:

```bash
# Create ZIP with exclusions
7z a -tzip mobile-shop-pos-v1.0.0.zip . -xr!.git -xr!.vscode -xr!.idea -xr!application/cache/* -xr!application/logs/*.log -x!.gitignore
```

---

## ✅ Verify ZIP Contents

Before uploading, verify ZIP contains:

**Must Have:**
- ✅ `application/` folder (with all controllers, models, views)
- ✅ `database/` folder (with all SQL files)
- ✅ `public/` folder (CSS, JS, images)
- ✅ `system/` folder (CodeIgniter core)
- ✅ `.env` file (with default/example values)
- ✅ `.htaccess` file
- ✅ `index.php`
- ✅ `README.md`
- ✅ `RELEASE_NOTES_v1.0.0.md`
- ✅ `GIT_RELEASE_COMMANDS.md`

**Must NOT Have:**
- ❌ `.git` folder
- ❌ `.gitignore`
- ❌ Cache files
- ❌ Log files
- ❌ IDE config folders

---

## 📤 Upload to GitHub Release

### Step 1: Go to GitHub Repository

```
https://github.com/YOUR_USERNAME/mobile-shop-pos
```

### Step 2: Navigate to Releases

1. Click **"Releases"** (right sidebar)
2. You should see your `v1.0.0` tag
3. Click **"Create release from tag"** or **"Edit"** if draft exists

### Step 3: Fill Release Form

**Tag:** `v1.0.0` (already created)

**Release Title:**
```
Mobile Shop POS v1.0.0 - Production Release 🎉
```

**Description:** (Copy from RELEASE_NOTES_v1.0.0.md)
```markdown
# 🎉 Mobile Shop POS v1.0.0 - Production Release

**Release Date:** December 31, 2024  
**Status:** ✅ Production Ready  
**Tested:** ✅ Client Approved

## 🌟 What's New

### Major Features
- ✅ Complete inventory management with IMEI tracking
- ✅ Real-time IMEI validation (no duplicates!)
- ✅ Customer credit/khata system with enable/disable
- ✅ Cost price & profit tracking
- ✅ Multiple payment methods
- ✅ Dashboard with analytics
- ✅ Pakistani business context optimization

### UI/UX Improvements
- ✅ No browser alerts - Custom notifications
- ✅ Email field removed (Pakistani context)
- ✅ Shop branding from .env
- ✅ Instant feedback on all actions

## 🐛 Bug Fixes
- ✅ Fixed dashboard division by zero
- ✅ Fixed inventory cost price display
- ✅ Fixed customer ledger errors
- ✅ Fixed IMEI duplicate detection

## 📦 Installation

1. Extract ZIP file
2. Create database: `mobile_shop_pos`
3. Import: `database/mobile_shop_pos_complete.sql`
4. Run SQL updates (see README.md)
5. Configure `.env` file
6. Access: `http://localhost/mobile-shop-pos/`
7. Login: admin@mobileshop.com / admin123

## 📚 Documentation
- Complete README.md included
- SQL migration files
- Setup instructions
- Troubleshooting guide

## ⚠️ Important
- Change default password after first login
- Backup database regularly
- Use HTTPS in production

**Made with ❤️ for Pakistani Mobile Shop Owners** 🇵🇰📱
```

### Step 4: Attach ZIP File

1. Scroll down to **"Attach binaries"**
2. Click or drag `mobile-shop-pos-v1.0.0.zip`
3. Wait for upload to complete

### Step 5: Publish Release

1. Check **"Set as the latest release"**
2. Click **"Publish release"**

---

## 🎉 Done!

Your release is now live at:
```
https://github.com/YOUR_USERNAME/mobile-shop-pos/releases/tag/v1.0.0
```

Users can download:
- **Source code (zip)** - Auto-generated by GitHub
- **Source code (tar.gz)** - Auto-generated by GitHub  
- **mobile-shop-pos-v1.0.0.zip** - Your clean package ✅

---

## 📊 Release Checklist

Before publishing:

- [ ] ZIP file created successfully
- [ ] ZIP size reasonable (< 50MB)
- [ ] Verified ZIP contents
- [ ] No sensitive data in ZIP
- [ ] README.md included
- [ ] SQL files included
- [ ] .env has safe defaults
- [ ] Release notes written
- [ ] Tag pushed to GitHub
- [ ] Release title set
- [ ] Release description added
- [ ] ZIP file attached
- [ ] "Latest release" checked
- [ ] Published!

---

## 🔄 Update Release (If Needed)

If you need to update after publishing:

1. Go to release page
2. Click **"Edit release"**
3. Update description or files
4. Click **"Update release"**

---

## 📞 Troubleshooting

**ZIP too large (> 100MB)?**
- Check if cache/logs are included
- Remove unnecessary files
- Use better compression (7-Zip)

**Upload fails?**
- Check internet connection
- Try smaller file
- Use GitHub CLI instead

**Can't find Releases tab?**
- Make sure repository is public
- Check repository settings
- Refresh page

---

**Ready to share with the world!** 🚀
