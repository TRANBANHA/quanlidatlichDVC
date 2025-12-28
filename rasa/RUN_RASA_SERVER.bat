@echo off
chcp 65001 >nul
title Rasa Server - Simple Run
color 0A

echo.
echo ========================================
echo    CHAY RASA SERVER - DON GIAN
echo ========================================
echo.

cd /d "%~dp0"

:: Kích hoạt virtual environment
echo [1/2] Kich hoat virtual environment...
if not exist "venv\Scripts\activate.bat" (
    echo [ERROR] Virtual environment chua ton tai!
    echo Vui long chay CHAY_RASA.bat de cai dat truoc.
    pause
    exit /b 1
)

call venv\Scripts\activate.bat
if errorlevel 1 (
    echo [ERROR] Khong the kich hoat virtual environment!
    pause
    exit /b 1
)
echo [OK] Virtual environment da duoc kich hoat
echo.

:: Kiểm tra Rasa
echo [2/2] Kiem tra Rasa...
:: Kiểm tra bằng cách kiểm tra file tồn tại
if not exist "venv\Scripts\rasa.exe" (
    echo [ERROR] Rasa chua duoc cai dat!
    echo Vui long chay CHAY_RASA.bat de cai dat truoc.
    pause
    exit /b 1
)

:: Kiểm tra xem Rasa có chạy được không (có thể thiếu dependencies)
python -c "import rasa; print('OK')" >nul 2>&1
if errorlevel 1 (
    echo [WARNING] Rasa da cai dat nhung co the thieu dependencies!
    echo [INFO] Dang cai dat dependencies con thieu...
    pip install scipy scikit-learn --quiet
    if errorlevel 1 (
        echo [WARNING] Khong the cai dat dependencies, nhung se thu chay server...
    ) else (
        echo [OK] Dependencies da duoc cai dat
    )
)

:: Thử chạy rasa --version
rasa --version >nul 2>&1
if errorlevel 1 (
    echo [WARNING] Khong the kiem tra version, nhung se thu chay server...
) else (
    rasa --version
    echo [OK] Rasa da san sang
)
echo.

:: Chạy Rasa server
echo ========================================
echo    RASA SERVER DANG CHAY
echo ========================================
echo.
echo    URL: http://localhost:5005
echo    Status: http://localhost:5005/status
echo.
echo    Nhan Ctrl+C de dung server
echo ========================================
echo.

rasa run --enable-api --cors "*" --port 5005

if errorlevel 1 (
    echo.
    echo [ERROR] Rasa server da dung do loi!
    pause
)

