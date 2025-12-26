# 🚀 Quick Start Guide - Mobile Shop POS

## 3 Simple Steps to Get Started

### ✅ Step 1: Import Database
1. Open phpMyAdmin: **http://localhost/phpmyadmin**
2. Click **"Import"** tab
3. Click **"Choose File"** → Select: `database/mobile_shop_pos_complete.sql`
4. Click **"Go"** button at the bottom

**Done!** The database is now ready with test data.

---

### ✅ Step 2: Access the System
Open your browser and go to:
```
http://localhost/mobile-shop-pos/
```

---

### ✅ Step 3: Login
```
Email:    admin@shop.com
Password: admin123
```

---

## 🎉 That's It!

You should now see a clean login page without any 1410 branding.

After login, you'll have access to:
- ✅ Dashboard with sales overview
- ✅ 5 test customers
- ✅ 14 products (8 accessories + 6 mobile phones)
- ✅ 15 phones with IMEI numbers ready to sell
- ✅ Full POS system ready to use

---

## 📝 What to Do Next

1. **Change your password** (recommended)
2. **Explore the dashboard** to see features
3. **Try making a test sale** with the sample data
4. **Add your own products** and customers
5. **Configure shop settings** as needed

---

## ❓ Having Issues?

### CSS/JS Not Loading?
- Clear browser cache (Ctrl+Shift+Delete)
- Make sure folder name is: `mobile-shop-pos`
- Check XAMPP Apache is running
- Verify base_url in `application/config/config.php` matches your folder name

### Can't Login?
- Verify you imported: `database/mobile_shop_pos_complete.sql`
- Check MySQL is running in XAMPP
- Try password: `admin123` (not the old MD5 one)

### Database Import Error?
- Make sure you have permission to drop/create databases
- Try logging into phpMyAdmin as root
- Check MySQL service is running

---

## 📚 More Help

- **Database Details**: See `database/README.md`
- **Full Setup Guide**: See `SETUP_COMPLETE.md`
- **PHP Fixes Applied**: See `PHP82_FIXES_APPLIED.md`

---

## 🔐 Security Note

**Important:** Change the default password immediately after first login!

The default password `admin123` is only for initial setup and testing.

---

**Enjoy your Mobile Shop POS System! 📱💰**
