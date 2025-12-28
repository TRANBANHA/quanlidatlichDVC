@echo off
echo ========================================
echo    KHOI DONG RASA CHATBOT SERVER
echo ========================================
echo.

cd /d "%~dp0"

echo [1/4] Kiem tra Python...
python --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Python chua duoc cai dat!
    echo Vui long cai dat Python 3.8+ tu https://www.python.org/downloads/
    pause
    exit /b 1
)
echo OK: Python da duoc cai dat
echo.

echo [2/4] Kiem tra Rasa...
rasa --version >nul 2>&1
if errorlevel 1 (
    echo Rasa chua duoc cai dat. Dang cai dat...
    pip install -r requirements.txt
    if errorlevel 1 (
        echo ERROR: Khong the cai dat Rasa!
        pause
        exit /b 1
    )
)
echo OK: Rasa da san sang
echo.

echo [3/4] Kiem tra model...
if not exist "models" (
    echo Model chua duoc train. Dang train model...
    rasa train
    if errorlevel 1 (
        echo ERROR: Khong the train model!
        pause
        exit /b 1
    )
)
echo OK: Model da san sang
echo.

echo [4/4] Khoi dong Rasa server...
echo.
echo ========================================
echo    RASA SERVER DANG CHAY
echo    URL: http://localhost:5005
echo    Nhan Ctrl+C de dung server
echo ========================================
echo.

rasa run --enable-api --cors "*" --port 5005

pause
