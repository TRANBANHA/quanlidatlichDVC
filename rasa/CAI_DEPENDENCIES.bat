@echo off
chcp 65001 >nul
title Cai dat Dependencies cho Rasa
color 0A

echo.
echo ========================================
echo    CAI DAT DEPENDENCIES CHO RASA
echo ========================================
echo.

cd /d "%~dp0"

:: Kích hoạt virtual environment
echo [1/2] Kich hoat virtual environment...
if not exist "venv\Scripts\activate.bat" (
    echo [ERROR] Virtual environment chua ton tai!
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

:: Cài đặt dependencies
echo [2/2] Cai dat dependencies...
echo [INFO] Dang cai dat scipy va scikit-learn...
pip install "scipy<1.9.0,>=1.4.1" "scikit-learn<1.2,>=0.22" --quiet
if errorlevel 1 (
    echo [WARNING] Co the co loi khi cai dat, nhung se thu tiep tuc...
) else (
    echo [OK] Dependencies da duoc cai dat
)

echo.
echo [INFO] Dang cai dat cac dependencies khac...
pip install "SQLAlchemy<1.5.0,>=1.4.0" "protobuf<3.20,>=3.9.2" --quiet
pip install "pydantic<1.10.3" "sanic-cors<2.1.0,>=2.0.0" "sanic-jwt<2.0.0,>=1.6.0" --quiet
pip install "sklearn-crfsuite<0.4,>=0.3" --quiet

echo.
echo [OK] Da hoan tat cai dat dependencies!
echo.
echo Ban co the chay RUN_RASA_SERVER.bat de khoi dong Rasa server.
echo.
pause

