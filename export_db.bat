@echo off
setlocal enabledelayedexpansion
title NTECH STORE - Database Exporter

echo ========================================================
echo        NTECH STORE - QUICK DATABASE EXPORTER SCRIPT
echo ========================================================
echo Kich ban nay se copy du lieu tu Database 'my_store' 
echo de ghi de (cap nhat) lai vao file database.sql.
echo.

:: 1. PROMPT CHO MYSQL PASSWORD
set "MYSQL_USER=root"
set "MYSQL_PASS="
echo [?] Thiet lap thong tin ket noi MySQL
set /p MYSQL_USER="Nhap MySQL Username (Nhan Enter de dung mac dinh 'root'): "
set /p MYSQL_PASS="Nhap MySQL Password (Nhan Enter neu xai Laragon/XAMPP mac dinh khong co pass): "

if "%MYSQL_USER%"=="" set "MYSQL_USER=root"

set "MYSQL_AUTH=-u %MYSQL_USER%"
if not "%MYSQL_PASS%"=="" set "MYSQL_AUTH=%MYSQL_AUTH% -p%MYSQL_PASS%"

:: 2. FIND MYSQLDUMP BINARY
set "MYSQLDUMP_BIN=mysqldump"
where mysqldump >nul 2>nul
if %ERRORLEVEL% equ 0 (
    echo [*] Tim thay mysqldump trong bien moi truong.
    goto :mysql_found
)

:: Fallback Laragon
pushd ..\..
set "LARAGON_DIR=%CD%"
popd
for /d %%I in ("%LARAGON_DIR%\bin\mysql\*") do (
    if exist "%%~fI\bin\mysqldump.exe" (
        set "MYSQLDUMP_BIN=%%~fI\bin\mysqldump.exe"
        echo [*] Tim thay mysqldump tai: !MYSQLDUMP_BIN!
        goto :mysql_found
    )
)

echo [ERROR] Khong tim thay mysqldump.exe! Ban co dang chay Laragon/XAMPP chua?
goto :error

:mysql_found
echo.

:: 3. EXPORT DATABASE
echo Dang xuat du lieu tu database 'my_store' ra file database.sql...
"%MYSQLDUMP_BIN%" %MYSQL_AUTH% my_store > database.sql
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Xuat du lieu that bai! Kiem tra lai thong tin tai khoan.
    goto :error
)

echo.
echo ========================================================
echo                 XUAT DU LIEU THANH CONG!
echo ========================================================
echo File database.sql da duoc cap nhat du lieu moi nhat.
echo Ban co the kiem tra lai va dung Git de Push len Github!
echo.
pause
exit /b 0

:error
echo.
echo ========================================================
echo        [!] QUA TRINH XUAT DU LIEU BI LOI [!]
echo ========================================================
echo Vui long doc thong bao loi phia tren de xu ly.
pause
exit /b 1
