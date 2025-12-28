@echo off
chcp 65001 >nul
title Rasa Server - Chay Don Gian
color 0A

echo.
echo ========================================
echo    CHAY RASA SERVER - DON GIAN
echo ========================================
echo.

cd /d "%~dp0"

:: Kích hoạt virtual environment
echo [1/3] Kich hoat virtual environment...
call venv\Scripts\activate.bat
if errorlevel 1 (
    echo [ERROR] Khong the kich hoat virtual environment!
    pause
    exit /b 1
)
echo [OK] Virtual environment da duoc kich hoat
echo.

:: Kiểm tra và cài đặt dependencies còn thiếu
echo [2/4] Kiem tra dependencies...
python -c "import aio_pika; print('OK')" >nul 2>&1
if errorlevel 1 (
    echo [INFO] Dang cai dat dependencies con thieu...
    pip install "aio-pika<8.2.4,>=6.7.1" "dask==2022.10.2" "matplotlib<3.6,>=3.1" "pydot<1.5,>=1.4" --quiet
    if errorlevel 1 (
        echo [WARNING] Co the co loi khi cai dat, nhung se thu tiep tuc...
    ) else (
        echo [OK] Dependencies da duoc cai dat
    )
) else (
    echo [OK] Dependencies da san sang
)
echo.

:: Kiểm tra model
echo [3/4] Kiem tra model...
if not exist "models" (
    echo [INFO] Model chua duoc train. Dang train model...
    echo [INFO] Qua trinh nay co the mat 1-5 phut. Vui long doi...
    echo.
    rasa train
    if errorlevel 1 (
        echo [ERROR] Khong the train model!
        echo [INFO] Kiem tra lai file config.yml va data/
        echo [INFO] Hoac co the thieu dependencies, thu chay: pip install -r requirements.txt
        pause
        exit /b 1
    )
    echo [OK] Model da duoc train thanh cong
) else (
    echo [OK] Model da ton tai
)
echo.

:: Chạy Rasa server
echo [4/4] Khoi dong Rasa server...
echo.
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

