@echo off
REM ============================================
REM Mobile Shop POS - Phase 1 Setup Script
REM For Windows with XAMPP
REM ============================================

echo.
echo ========================================
echo Mobile Shop POS - Phase 1 Setup
echo ========================================
echo.

REM Set MySQL path
set MYSQL_PATH=C:\xampp\mysql\bin
set PATH=%PATH%;%MYSQL_PATH%

echo [1/5] Checking MySQL installation...
mysql --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: MySQL not found!
    echo Please ensure XAMPP is installed and MySQL path is correct.
    echo Current path: %MYSQL_PATH%
    pause
    exit /b 1
)
echo MySQL found!
echo.

echo [2/5] Creating database...
echo Please enter MySQL root password (press Enter if no password):
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS mobile_shop_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    echo ERROR: Failed to create database!
    pause
    exit /b 1
)
echo Database 'mobile_shop_pos' created successfully!
echo.

echo [3/5] Importing base schema...
echo Please enter MySQL root password again:
mysql -u root -p mobile_shop_pos < 1410inventory.sql
if errorlevel 1 (
    echo ERROR: Failed to import base schema!
    pause
    exit /b 1
)
echo Base schema imported successfully!
echo.

echo [4/5] Running Phase 1 migration...
echo Please enter MySQL root password again:
mysql -u root -p mobile_shop_pos < database\migrations\phase1_mobile_shop_complete.sql
if errorlevel 1 (
    echo ERROR: Failed to run migration!
    pause
    exit /b 1
)
echo Phase 1 migration completed successfully!
echo.

echo [5/5] Verifying installation...
echo Please enter MySQL root password one last time:
mysql -u root -p mobile_shop_pos -e "SHOW TABLES; SELECT 'Verification Complete' as Status;"
echo.

echo ========================================
echo Phase 1 Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Edit .env file with your settings
echo 2. Start Apache and MySQL in XAMPP
echo 3. Access: http://localhost/mini-inventory-and-sales-management-system/
echo 4. Login: demo@1410inc.xyz / demopass
echo 5. Start Phase 2 development
echo.
echo Database: mobile_shop_pos
echo Tables: 8 (4 new, 2 modified)
echo Views: 3
echo.
pause
