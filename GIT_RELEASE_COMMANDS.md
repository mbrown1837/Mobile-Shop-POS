# 🚀 Git Release Commands

Quick reference for creating GitHub releases.

## 📋 Pre-Release Checklist

- [ ] All changes committed
- [ ] Database exported
- [ ] Documentation updated
- [ ] Version number updated
- [ ] Release package tested

## 🎯 Create Release

### Step 1: Commit All Changes

```bash
git add .
git commit -m "Release v1.1.0 - Customer ledger system with fixes"
```

### Step 2: Create Tag

```bash
git tag -a v1.1.0 -m "Version 1.1.0 - Customer ledger system"
```

### Step 3: Push to GitHub

```bash
git push origin main
git push origin v1.1.0
```

### Step 4: Create GitHub Release

1. Go to: `https://github.com/YOUR_USERNAME/mobile-shop-pos/releases`
2. Click **"Create a new release"**
3. Select tag: `v1.1.0`
4. Release title: `Mobile Shop POS v1.1.0`
5. Description:
   ```markdown
   ## 🎉 Mobile Shop POS v1.1.0
   
   Complete point of sale system for mobile shops with customer ledger management.
   
   ### ✨ Features
   - POS system with cart management
   - Inventory management (serialized & standard items)
   - Customer ledger (khata) system
   - Sales reports and profit tracking
   - IMEI tracking for mobile phones
   - Credit/cash payment options
   
   ### 📦 Installation
   See [QUICK_SETUP.md](QUICK_SETUP.md) for 5-minute setup guide.
   
   ### 📚 Documentation
   - [Installation Guide](INSTALLATION_GUIDE.md)
   - [Quick Setup](QUICK_SETUP.md)
   - [System Verification](SYSTEM_VERIFICATION_CHECKLIST.md)
   
   ### 🔐 Default Login
   - Username: `admin`
   - Password: `admin123`
   
   **⚠️ Change password after first login!**
   ```

6. Upload `mobile-shop-pos-v1.1.0.zip`
7. Click **"Publish release"**

## 🔄 Update Existing Release

### Update Tag:
```bash
git tag -d v1.1.0
git push origin :refs/tags/v1.1.0
git tag -a v1.1.0 -m "Version 1.1.0 - Updated"
git push origin v1.1.0
```

### Update Files:
1. Go to release page
2. Click **"Edit release"**
3. Upload new files
4. Update description
5. Click **"Update release"**

## 📝 Version Naming

Follow semantic versioning:
- `v1.0.0` - Major release
- `v1.1.0` - Minor update (new features)
- `v1.1.1` - Patch (bug fixes)

## 🎯 Quick Commands

### View Tags:
```bash
git tag
```

### Delete Local Tag:
```bash
git tag -d v1.1.0
```

### Delete Remote Tag:
```bash
git push origin :refs/tags/v1.1.0
```

### View Commit History:
```bash
git log --oneline
```

### Create Release Branch:
```bash
git checkout -b release/v1.1.0
```

## ✅ Post-Release

- [ ] Verify release on GitHub
- [ ] Test download link
- [ ] Update README with release link
- [ ] Announce release
- [ ] Monitor for issues

---

**Current Version**: v1.1.0  
**Repository**: https://github.com/YOUR_USERNAME/mobile-shop-pos
