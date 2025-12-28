@echo off
chcp 65001 >nul
title Cai dat Tat ca Dependencies cho Rasa
color 0A

echo.
echo ========================================
echo    CAI DAT TAT CA DEPENDENCIES CHO RASA
echo ========================================
echo.
echo [INFO] Qua trinh nay co the mat 5-10 phut. Vui long doi...
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

:: Cài đặt tất cả dependencies quan trọng
echo [2/3] Cai dat dependencies...
echo [INFO] Dang cai dat cac dependencies quan trong...
echo.

:: Core dependencies
echo [INFO] Core dependencies...
pip install "scipy<1.9.0,>=1.4.1" "scikit-learn<1.2,>=0.22" --quiet
pip install "protobuf<3.20,>=3.9.2" "pydantic<1.10.3" --quiet
pip install "SQLAlchemy<1.5.0,>=1.4.0" --quiet
pip install "sanic-cors<2.1.0,>=2.0.0" "sanic-jwt<2.0.0,>=1.6.0" --quiet
pip install "sklearn-crfsuite<0.4,>=0.3" --quiet

:: Dependencies cho training
echo [INFO] Dependencies cho training...
pip install "aio-pika<8.2.4,>=6.7.1" --quiet
pip install "boto3<2.0.0,>=1.26.136" --quiet
pip install "dask==2022.10.2" --quiet
pip install "matplotlib<3.6,>=3.1" --quiet
pip install "pydot<1.5,>=1.4" --quiet

:: Optional dependencies (có thể bỏ qua nếu lỗi)
echo [INFO] Optional dependencies...
pip install "redis<5.0,>=4.5.3" --quiet 2>nul
pip install "python-engineio!=5.0.0,<6,>=4" --quiet 2>nul
pip install "python-socketio<6,>=4.4" --quiet 2>nul

echo.
echo [OK] Da hoan tat cai dat dependencies!
echo.

:: Kiểm tra Rasa
echo [3/3] Kiem tra Rasa...
python -c "import rasa; print('Rasa OK')" 2>nul
if errorlevel 1 (
    echo [WARNING] Rasa co the van thieu mot so dependencies
    echo [INFO] Ban co the thu train model de xem con thieu gi
) else (
    echo [OK] Rasa da san sang!
)
echo.
echo [INFO] Ban co the chay CHAY_RASA_DON_GIAN.bat de train model va chay server.
echo.
pause

