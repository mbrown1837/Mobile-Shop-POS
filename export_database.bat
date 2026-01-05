@echo off
REM Export current database with all fixes
REM This creates a clean database file for distribution

echo ========================================
echo Mobile Shop POS - Database Export
echo ========================================
echo.

REM Set your MySQL path (adjust if different)
set MYSQL_PATH=C:\xampp\mysql\bin

REM Database credentials
set DB_HOST=localhost
set DB_USER=root
set DB_PASS=
set DB_NAME=mobile_shop_pos

REM Output file
set OUTPUT_FILE=database\mobile_shop_pos_v1.1.0_final.sql

echo Exporting database: %DB_NAME%
echo Output file: %OUTPUT_FILE%
echo.

REM Export database
"%MYSQL_PATH%\mysqldump.exe" -h %DB_HOST% -u %DB_USER% %DB_NAME% > %OUTPUT_FILE%

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo SUCCESS! Database exported successfully
    echo ========================================
    echo.
    echo File created: %OUTPUT_FILE%
    echo File size:
    dir "%OUTPUT_FILE%" | find "mobile_shop_pos"
    echo.
    echo This file is ready for distribution!
) else (
    echo.
    echo ========================================
    echo ERROR! Database export failed
    echo ========================================
    echo.
    echo Please check:
    echo 1. MySQL is running
    echo 2. Database name is correct
    echo 3. MySQL path is correct
    echo 4. You have proper permissions
)

echo.
pause
