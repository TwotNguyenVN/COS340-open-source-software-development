@echo off
setlocal enabledelayedexpansion
title NTECH STORE - Auto Installer

echo ========================================================
echo        NTECH STORE - QUICK INSTALLATION SCRIPT
echo ========================================================
echo.

:: 1. SANITY CHECKS
if not exist "database.sql" (
    echo [ERROR] Khong tim thay file database.sql!
    echo Vui long dat file install.bat o cung thu muc voi file database.sql.
    goto :error
)

if not exist "composer.json" (
    echo [ERROR] Khong tim thay file composer.json!
    echo Vui long dam bao ban dang o dung thu muc goc cua du an.
    goto :error
)

:: 2. PROMPT CHO MYSQL PASSWORD
set "MYSQL_USER=root"
set "MYSQL_PASS="
echo [?] Thiet lap thong tin ket noi MySQL
set /p MYSQL_USER="Nhap MySQL Username (Nhan Enter de dung mac dinh 'root'): "
set /p MYSQL_PASS="Nhap MySQL Password (Nhan Enter neu xai Laragon/XAMPP mac dinh khong co pass): "

if "%MYSQL_USER%"=="" set "MYSQL_USER=root"

set "MYSQL_AUTH=-u %MYSQL_USER%"
if not "%MYSQL_PASS%"=="" set "MYSQL_AUTH=%MYSQL_AUTH% -p%MYSQL_PASS%"

:: 3. FIND MYSQL BINARY
set "MYSQL_BIN=mysql"
where mysql >nul 2>nul
if %ERRORLEVEL% equ 0 (
    echo [*] Tim thay MySQL trong bien moi truong.
    goto :mysql_found
)

:: Fallback Laragon
pushd ..\..
set "LARAGON_DIR=%CD%"
popd
for /d %%I in ("%LARAGON_DIR%\bin\mysql\*") do (
    if exist "%%~fI\bin\mysql.exe" (
        set "MYSQL_BIN=%%~fI\bin\mysql.exe"
        echo [*] Tim thay MySQL tai: !MYSQL_BIN!
        goto :mysql_found
    )
)

echo [ERROR] Khong tim thay mysql.exe! Ban co dang chay Laragon/XAMPP va da bat MySQL chua?
goto :error

:mysql_found
echo.

:: 4. INIT DATABASE
echo [1/2] Dang khoi tao va nap du lieu vao Database 'my_store'...
"%MYSQL_BIN%" %MYSQL_AUTH% -e "CREATE DATABASE IF NOT EXISTS my_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Tao database that bai! Kiem tra lai tai khoan hoac mat khau MySQL.
    goto :error
)

"%MYSQL_BIN%" %MYSQL_AUTH% --default-character-set=utf8mb4 my_store < database.sql
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Import database.sql that bai!
    goto :error
)
echo   - Xong! Database da duoc import thanh cong.
echo.

:: 5. FIND COMPOSER & PHP
echo [2/2] Dang cai dat cac thu vien phu thuoc bang Composer (VD: php-jwt)...
set "COMPOSER_BIN=composer"
where composer >nul 2>nul
if %ERRORLEVEL% equ 0 (
    echo [*] Tim thay Composer toan cuc. Dang chay 'composer install'...
    call composer install
    if !ERRORLEVEL! neq 0 goto :error
    goto :finish
)

:: Fallback Laragon PHP & Composer
set "PHP_BIN="
for /d %%I in ("%LARAGON_DIR%\bin\php\*") do (
    if exist "%%~fI\php.exe" (
        set "PHP_BIN=%%~fI\php.exe"
        goto :php_found
    )
)
:php_found

set "COMPOSER_PHAR=%LARAGON_DIR%\bin\composer\composer.phar"

if not "!PHP_BIN!"=="" (
    if exist "!COMPOSER_PHAR!" (
        echo [*] Dung PHP: !PHP_BIN!
        echo [*] Dung Composer: !COMPOSER_PHAR!
        "!PHP_BIN!" "!COMPOSER_PHAR!" install
        if !ERRORLEVEL! neq 0 goto :error
        goto :finish
    )
)

echo [ERROR] Khong tim thay lenh Composer!
echo Ban van co the chay Code, nhung can cai dat thu vien thu cong bang lenh 'composer install' de dung duoc JWT.
goto :error

:finish
echo.
echo ========================================================
echo                 CAI DAT THANH CONG!
echo ========================================================
echo [*] Vui long kiem tra file: app/config/database.php de 
echo     dam bao thong tin ket noi trung khop voi may cua ban.
echo [*] Truoc khi truy cap, hay khoi dong Apache/Nginx tren Laragon.
echo [*] Link truy cap mac dinh (Thay doi PORT neu can):
echo     http://localhost:88/4851_NguyenNgocTinh_WebsiteBanHang/
echo.
echo Chuc ban mot ngay code vui ve!
pause
exit /b 0

:error
echo.
echo ========================================================
echo        [!] QUA TRINH CAI DAT BI LOI (FAILED) [!]
echo ========================================================
echo Vui long doc thong bao loi phia tren de xu ly.
pause
exit /b 1
