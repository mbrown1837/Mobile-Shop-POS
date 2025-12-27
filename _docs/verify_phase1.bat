@echo off
REM ============================================
REM Mobile Shop POS - Phase 1 Verification
REM ============================================

echo.
echo ========================================
echo Phase 1 Verification Script
echo ========================================
echo.

set MYSQL_PATH=C:\xampp\mysql\bin
set PATH=%PATH%;%MYSQL_PATH%

echo Checking Phase 1 installation...
echo.
echo Please enter MySQL root password:
echo.

mysql -u root -p mobile_shop_pos -e "SELECT 'Database Connection' as Test, 'PASSED' as Status UNION ALL SELECT 'Tables Check', CASE WHEN COUNT(*) = 8 THEN 'PASSED' ELSE 'FAILED' END FROM information_schema.tables WHERE table_schema = 'mobile_shop_pos' AND table_type = 'BASE TABLE' UNION ALL SELECT 'Views Check', CASE WHEN COUNT(*) = 3 THEN 'PASSED' ELSE 'FAILED' END FROM information_schema.views WHERE table_schema = 'mobile_shop_pos' UNION ALL SELECT 'Items Columns', CASE WHEN COUNT(*) >= 13 THEN 'PASSED' ELSE 'FAILED' END FROM information_schema.columns WHERE table_schema = 'mobile_shop_pos' AND table_name = 'items' UNION ALL SELECT 'item_serials Table', CASE WHEN COUNT(*) > 0 THEN 'PASSED' ELSE 'FAILED' END FROM information_schema.tables WHERE table_schema = 'mobile_shop_pos' AND table_name = 'item_serials' UNION ALL SELECT 'customers Table', CASE WHEN COUNT(*) > 0 THEN 'PASSED' ELSE 'FAILED' END FROM information_schema.tables WHERE table_schema = 'mobile_shop_pos' AND table_name = 'customers';"

echo.
echo ========================================
echo Detailed Table List:
echo ========================================
mysql -u root -p mobile_shop_pos -e "SHOW TABLES;"

echo.
echo ========================================
echo Views List:
echo ========================================
mysql -u root -p mobile_shop_pos -e "SHOW FULL TABLES WHERE Table_type = 'VIEW';"

echo.
echo ========================================
echo Items Table Structure (New Columns):
echo ========================================
mysql -u root -p mobile_shop_pos -e "DESCRIBE items;" | findstr /C:"brand" /C:"model" /C:"category" /C:"item_type" /C:"warranty"

echo.
echo ========================================
echo Verification Complete!
echo ========================================
echo.
echo If all tests show PASSED, Phase 1 is successful!
echo.
pause
