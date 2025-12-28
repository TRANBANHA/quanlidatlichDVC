# Hướng dẫn chạy Rasa Chatbot

## Yêu cầu hệ thống

- Python 3.8 trở lên
- pip (Python package manager)

## Bước 1: Cài đặt Python và pip

### Windows (Laragon):
Laragon đã có sẵn Python, kiểm tra bằng lệnh:
```bash
python --version
pip --version
```

Nếu chưa có, tải từ: https://www.python.org/downloads/

## Bước 2: Cài đặt Rasa

Mở terminal/PowerShell trong thư mục `rasa`:

```bash
cd rasa
pip install -r requirements.txt
```

Hoặc cài trực tiếp:
```bash
pip install rasa==3.6.0
pip install rasa-sdk==3.6.0
```

## Bước 3: Train model

Sau khi cài đặt xong, train model:

```bash
rasa train
```

Lệnh này sẽ:
- Đọc các file trong `data/nlu/`, `data/stories/`, `data/rules/`
- Train model NLU và Dialogue
- Tạo model trong thư mục `models/`

## Bước 4: Chạy Rasa server

### Chạy với API (cho Laravel):

```bash
rasa run --enable-api --cors "*" --port 5005
```

Hoặc:

```bash
rasa run --enable-api --cors "*"
```

Server sẽ chạy tại: `http://localhost:5005`

### Chạy với shell (để test):

```bash
rasa shell
```

## Bước 5: Kiểm tra kết nối

Mở trình duyệt hoặc dùng curl:

```bash
curl http://localhost:5005/status
```

Hoặc test API:

```bash
curl -X POST http://localhost:5005/webhooks/rest/webhook \
  -H "Content-Type: application/json" \
  -d '{"sender": "test_user", "message": "xin chào"}'
```

## Bước 6: Cấu hình trong Laravel

Thêm vào file `.env`:

```env
RASA_URL=http://localhost:5005
RASA_PORT=5005
```

## Chạy tự động khi khởi động máy (Windows)

Tạo file `start_rasa.bat` trong thư mục `rasa`:

```batch
@echo off
cd /d "%~dp0"
rasa run --enable-api --cors "*" --port 5005
pause
```

Hoặc tạo Windows Service để chạy nền.

## Troubleshooting

### Lỗi: "rasa: command not found"
- Đảm bảo Python đã được cài đặt
- Thêm Python vào PATH
- Hoặc dùng: `python -m rasa` thay vì `rasa`

### Lỗi: Port 5005 đã được sử dụng
- Đổi port: `rasa run --enable-api --cors "*" --port 5006`
- Hoặc kill process đang dùng port 5005

### Lỗi khi train model
- Kiểm tra cú pháp trong `data/nlu/nlu.yml`, `data/stories/stories.yml`
- Đảm bảo format YAML đúng

## Cải thiện bot

1. Thêm training data vào `data/nlu/nlu.yml`
2. Thêm stories vào `data/stories/stories.yml`
3. Train lại: `rasa train`
4. Restart server

## Test nhanh

```bash
# Test trong shell
rasa shell

# Test API
curl -X POST http://localhost:5005/webhooks/rest/webhook \
  -H "Content-Type: application/json" \
  -d '{"sender": "user1", "message": "xin chào"}'
```
