@echo off
chcp 65001 >nul
title Rasa Chatbot Server - All in One
color 0A

echo.
echo ========================================
echo    RASA CHATBOT SERVER - ALL IN ONE
echo ========================================
echo.

cd /d "%~dp0"

:: ============================================
:: BƯỚC 1: Kiểm tra Python
:: ============================================
echo [1/7] Kiểm tra Python...
python --version >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Python chưa được cài đặt!
    echo Vui lòng cài đặt Python 3.8+ từ: https://www.python.org/downloads/
    echo.
    pause
    exit /b 1
)
python --version
echo [OK] Python đã được cài đặt
echo.

:: ============================================
:: BƯỚC 2: Tạo/Kích hoạt virtual environment
:: ============================================
echo [2/7] Kiểm tra virtual environment...
if not exist "venv" (
    echo [INFO] Virtual environment chưa tồn tại. Đang tạo...
    python -m venv venv
    if errorlevel 1 (
        echo [ERROR] Không thể tạo virtual environment!
        pause
        exit /b 1
    )
    echo [OK] Virtual environment đã được tạo
) else (
    echo [OK] Virtual environment đã tồn tại
)
echo.

echo [2.5/7] Kích hoạt virtual environment...
call venv\Scripts\activate.bat
if errorlevel 1 (
    echo [ERROR] Không thể kích hoạt virtual environment!
    pause
    exit /b 1
)
echo [OK] Virtual environment đã được kích hoạt
echo.

:: ============================================
:: BƯỚC 3: Cài đặt pip mới nhất
:: ============================================
echo [3/7] Cập nhật pip...
python -m pip install --upgrade pip --quiet
echo [OK] pip đã được cập nhật
echo.

:: ============================================
:: BƯỚC 4: Cài đặt Rasa và dependencies
:: ============================================
echo [4/7] Kiểm tra và cài đặt Rasa...
rasa --version >nul 2>&1
if errorlevel 1 (
    echo [INFO] Rasa chưa được cài đặt. Đang cài đặt...
    echo [INFO] Quá trình này có thể mất vài phút. Vui lòng đợi...
    echo.
    
    :: Cài đặt dependencies cốt lõi trước (chia nhỏ để tránh treo)
    echo [INFO] Đang cài đặt dependencies cốt lõi...
    echo [INFO] Quá trình này có thể mất vài phút. Vui lòng đợi...
    echo.
    
    echo [INFO] Nhóm 1/4: absl-py, aiohttp, apscheduler, attrs...
    pip install "absl-py<1.5,>=0.9" "aiohttp<3.9,!=3.7.4.post0,>=3.6" "apscheduler<3.10,>=3.6" "attrs<22.2,>=19.3"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong nhóm 1/4
    )
    echo.
    
    echo [INFO] Nhóm 2/4: cloudpickle, colorama, coloredlogs, joblib...
    pip install "cloudpickle<2.3,>=1.2" "colorama<0.5.0,>=0.4.4" "coloredlogs<16,>=10" "joblib<1.3.0,>=0.15.1"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong nhóm 2/4
    )
    echo.
    
    echo [INFO] Nhóm 3/4: jsonpickle, jsonschema, numpy, packaging...
    pip install "jsonpickle<3.1,>=1.3" "jsonschema<4.18,>=3.2" "numpy<1.25.0,>=1.19.2" "packaging<21.0,>=20.0"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong nhóm 3/4
    )
    echo.
    
    echo [INFO] Nhóm 4/4: pyyaml, requests, ruamel.yaml, sanic, tqdm...
    pip install "pyyaml<6.0,>=5.3.1" "requests<3.0,>=2.23" "ruamel.yaml<0.18.0,>=0.16.5" "sanic<21.13,>=21.12" "tqdm<5.0,>=4.31" "typing-extensions<5.0.0,>=4.1.1" "websockets<11.0,>=10.0"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong nhóm 4/4
    )
    echo.
    
    echo [OK] Đã hoàn tất cài đặt dependencies cốt lõi
    
    :: Cài đặt Rasa với --no-deps
    echo [INFO] Đang cài đặt Rasa...
    pip install rasa==3.6.0 rasa-sdk==3.6.0 --no-deps --quiet
    
    :: Cài đặt dependencies bổ sung (chia nhỏ để tránh treo)
    echo [INFO] Đang cài đặt dependencies bổ sung...
    echo [INFO] Bước 1/4: Cài đặt prompt-toolkit, python-dateutil, pytz, regex...
    pip install "prompt-toolkit<3.0.29,>=3.0" "python-dateutil<2.9,>=2.8" "pytz<2023.0,>=2019.1" "regex<2022.11,>=2020.6"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong bước 1/4
    )
    echo.
    
    echo [INFO] Bước 2/4: Cài đặt terminaltables, structlog, portalocker...
    pip install "terminaltables<3.2.0,>=3.1.0" "structlog<24.0.0,>=23.1.0" "structlog-sentry<3.0.0,>=2.0.2" "portalocker<3.0.0,>=2.7.0"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong bước 2/4
    )
    echo.
    
    echo [INFO] Bước 3/4: Cài đặt networkx, colorclass, colorhash, CacheControl...
    pip install "networkx<2.7,>=2.4" "colorclass<2.3,>=2.2" "colorhash<1.3.0,>=1.0.2" "CacheControl<0.13.0,>=0.12.9" "PyJWT[crypto]<3.0.0,>=2.0.0"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong bước 3/4
    )
    echo.
    
    echo [INFO] Bước 4/4: Cài đặt pykwalify, questionary, randomname, tarsafe...
    pip install "pykwalify<1.9,>=1.7" "questionary<1.11.0,>=1.5.1" "randomname<0.2.0,>=0.1.5" "tarsafe<0.0.5,>=0.0.3" "typing-utils<0.2.0,>=0.1.0" "sentry-sdk<1.15.0,>=0.17.0"
    if errorlevel 1 (
        echo [WARNING] Một số packages có thể không cài được, tiếp tục...
    ) else (
        echo [OK] Đã cài đặt xong bước 4/4
    )
    
    echo [OK] Đã hoàn tất cài đặt dependencies bổ sung
    echo.
    
    :: Kiểm tra lại
    echo [INFO] Đang kiểm tra Rasa...
    rasa --version >nul 2>&1
    if errorlevel 1 (
        echo [WARNING] Rasa có thể chưa hoàn chỉnh nhưng sẽ thử chạy...
    ) else (
        rasa --version
        echo [OK] Rasa đã được cài đặt thành công
    )
    
    :: Kiểm tra xem Rasa có import được không
    echo [INFO] Kiểm tra Rasa có thể import được không...
    python -c "import rasa; print('OK')" 2>nul
    if errorlevel 1 (
        echo [WARNING] Rasa không thể import được, có thể thiếu dependencies
        echo [INFO] Đang thử cài đặt lại các dependencies còn thiếu...
        pip install "pykwalify<1.9,>=1.7" "structlog-sentry<3.0.0,>=2.0.2" "questionary<1.11.0,>=1.5.1" "randomname<0.2.0,>=0.1.5" "tarsafe<0.0.5,>=0.0.3" "typing-utils<0.2.0,>=0.1.0" "sentry-sdk<1.15.0,>=0.17.0"
    ) else (
        echo [OK] Rasa có thể import được
    )
) else (
    :: Kiểm tra xem Rasa có chạy được không
    echo [INFO] Rasa đã được cài đặt, đang kiểm tra dependencies...
    python -c "import rasa; print('OK')" 2>nul
    if errorlevel 1 (
        echo [WARNING] Rasa có thể thiếu một số dependencies
        echo [INFO] Đang cài đặt dependencies cần thiết...
        pip install "pykwalify<1.9,>=1.7" "structlog-sentry<3.0.0,>=2.0.2" "questionary<1.11.0,>=1.5.1" "randomname<0.2.0,>=0.1.5" "tarsafe<0.0.5,>=0.0.3" "typing-utils<0.2.0,>=0.1.0" "sentry-sdk<1.15.0,>=0.17.0" --quiet
    )
    rasa --version >nul 2>&1
    if errorlevel 1 (
        echo [WARNING] Rasa có thể chưa hoàn chỉnh nhưng sẽ thử chạy...
    ) else (
        rasa --version
    )
    echo [OK] Rasa đã sẵn sàng
)
echo.
echo [INFO] Tiếp tục đến bước tiếp theo...
echo.

:: ============================================
:: BƯỚC 5: Cài đặt Core Dependencies (TensorFlow, scikit-learn, etc.)
:: ============================================
echo [5/7] Kiểm tra Core Dependencies (TensorFlow, scikit-learn)...
echo [INFO] Đang kiểm tra xem TensorFlow và scikit-learn đã được cài đặt chưa...
python -c "import tensorflow; import sklearn; print('OK')" 2>nul
if errorlevel 1 (
    echo [INFO] Core dependencies chưa được cài đặt. Đang cài đặt...
    echo [INFO] QUAN TRỌNG: Quá trình này có thể mất 5-15 phút. Vui lòng đợi...
    echo [INFO] KHÔNG nhấn Ctrl+C trong lúc này, nếu không script sẽ dừng!
    echo.
    
    :: Cài đặt TensorFlow cho Windows (có thể bỏ qua nếu lỗi)
    echo [INFO] [1/5] Đang cài đặt TensorFlow (có thể mất 5-10 phút)...
    echo [INFO] Đây là package lớn nhất, vui lòng kiên nhẫn đợi...
    pip install "tensorflow-intel==2.11.1" --quiet --timeout 600
    if errorlevel 1 (
        echo [WARNING] TensorFlow không cài được, bỏ qua...
        echo [INFO] Rasa vẫn có thể chạy được nhưng một số tính năng ML có thể không hoạt động
    ) else (
        echo [OK] TensorFlow đã được cài đặt
        pip install "tensorflow-io-gcs-filesystem<0.32,>=0.23.1" "tensorflow_hub<0.13.0,>=0.12.0" --quiet
    )
    echo.
    
    :: Cài đặt scikit-learn và scipy (quan trọng)
    echo [INFO] [2/5] Đang cài đặt scikit-learn và scipy (quan trọng)...
    pip install "scikit-learn<1.2,>=0.22" "scipy<1.9.0,>=1.4.1" --quiet --timeout 300
    if errorlevel 1 (
        echo [WARNING] scikit-learn/scipy có thể không cài được, tiếp tục...
    ) else (
        echo [OK] scikit-learn và scipy đã được cài đặt
    )
    echo.
    
    :: Cài đặt SQLAlchemy và protobuf
    echo [INFO] [3/5] Đang cài đặt SQLAlchemy và protobuf...
    pip install "SQLAlchemy<1.5.0,>=1.4.0" "protobuf<3.20,>=3.9.2" --quiet
    if errorlevel 1 (
        echo [WARNING] SQLAlchemy/protobuf có thể không cài được, tiếp tục...
    ) else (
        echo [OK] SQLAlchemy và protobuf đã được cài đặt
    )
    echo.
    
    :: Cài đặt pydantic và sanic extensions
    echo [INFO] [4/5] Đang cài đặt pydantic và sanic extensions...
    pip install "pydantic<1.10.3" "sanic-cors<2.1.0,>=2.0.0" "sanic-jwt<2.0.0,>=1.6.0" --quiet
    if errorlevel 1 (
        echo [WARNING] pydantic/sanic extensions có thể không cài được, tiếp tục...
    ) else (
        echo [OK] pydantic và sanic extensions đã được cài đặt
    )
    echo.
    
    :: Cài đặt sklearn-crfsuite
    echo [INFO] [5/5] Đang cài đặt sklearn-crfsuite...
    pip install "sklearn-crfsuite<0.4,>=0.3" --quiet
    if errorlevel 1 (
        echo [WARNING] sklearn-crfsuite có thể không cài được, tiếp tục...
    ) else (
        echo [OK] sklearn-crfsuite đã được cài đặt
    )
    echo.
    
    echo [OK] Đã hoàn tất cài đặt Core Dependencies
) else (
    echo [OK] Core dependencies đã sẵn sàng
)
echo.

:: ============================================
:: BƯỚC 6: Kiểm tra và train model
:: ============================================
echo [6/7] Kiểm tra model...
if not exist "models" (
    echo [INFO] Model chưa được train. Đang train model...
    echo [INFO] Quá trình này có thể mất 1-5 phút. Vui lòng đợi...
    echo.
    
    :: Kiểm tra xem Rasa có chạy được không trước khi train
    python -c "import rasa; print('Rasa OK')" 2>nul
    if errorlevel 1 (
        echo [ERROR] Rasa không thể import được!
        echo [INFO] Có thể thiếu dependencies. Đang thử cài đặt lại...
        pip install "pykwalify<1.9,>=1.7" "structlog-sentry<3.0.0,>=2.0.2" "questionary<1.11.0,>=1.5.1" "randomname<0.2.0,>=0.1.5" "tarsafe<0.0.5,>=0.0.3" "typing-utils<0.2.0,>=0.1.0" "sentry-sdk<1.15.0,>=0.17.0"
        echo.
        echo [INFO] Kiểm tra lại...
        python -c "import rasa; print('Rasa OK')" 2>nul
        if errorlevel 1 (
            echo [ERROR] Rasa vẫn không thể import được!
            echo [INFO] Bỏ qua train model, sẽ thử chạy server với model cũ (nếu có)
            echo.
        ) else (
            echo [OK] Rasa đã có thể import được, tiếp tục train model...
            rasa train
            if errorlevel 1 (
                echo [WARNING] Không thể train model, nhưng sẽ thử chạy server...
            ) else (
                echo [OK] Model đã được train thành công
            )
        )
    ) else (
        rasa train
        if errorlevel 1 (
            echo [WARNING] Không thể train model!
            echo [INFO] Kiểm tra lại file config.yml và data/
            echo [INFO] Bạn vẫn có thể thử chạy server nếu model cũ tồn tại
            echo.
        ) else (
            echo [OK] Model đã được train thành công
        )
    )
) else (
    echo [OK] Model đã tồn tại
)
echo.

:: ============================================
:: BƯỚC 7: Chạy Rasa server
:: ============================================
echo [7/7] Khởi động Rasa server...
echo.
echo ========================================
echo    RASA SERVER ĐANG CHẠY
echo ========================================
echo.
echo    URL: http://localhost:5005
echo    Status: http://localhost:5005/status
echo.
echo    Nhấn Ctrl+C để dừng server
echo ========================================
echo.

:: Kiểm tra lại Rasa trước khi chạy
echo [INFO] Kiểm tra Rasa trước khi khởi động server...
python -c "import rasa; print('Rasa OK')" 2>nul
if errorlevel 1 (
    echo [ERROR] Rasa không thể import được!
    echo [INFO] Có thể thiếu dependencies quan trọng.
    echo [INFO] Đang thử cài đặt lại các dependencies còn thiếu...
    pip install "pykwalify<1.9,>=1.7" "structlog-sentry<3.0.0,>=2.0.2" "questionary<1.11.0,>=1.5.1" "randomname<0.2.0,>=0.1.5" "tarsafe<0.0.5,>=0.0.3" "typing-utils<0.2.0,>=0.1.0" "sentry-sdk<1.15.0,>=0.17.0"
    echo.
    echo [INFO] Kiểm tra lại...
    python -c "import rasa; print('Rasa OK')" 2>nul
    if errorlevel 1 (
        echo [ERROR] Rasa vẫn không thể import được!
        echo [INFO] Vui lòng kiểm tra lại dependencies hoặc chạy lại script.
        echo.
        pause
        exit /b 1
    )
)
echo [OK] Rasa đã sẵn sàng để chạy
echo.

rasa run --enable-api --cors "*" --port 5005

if errorlevel 1 (
    echo.
    echo [ERROR] Rasa server đã dừng do lỗi!
    echo [INFO] Kiểm tra lại cấu hình hoặc model
    echo.
    pause
)
