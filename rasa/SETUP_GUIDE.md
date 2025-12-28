# Hướng dẫn Setup và Chạy Rasa Chatbot

## Bước 1: Cài đặt Python

1. Tải Python 3.8 trở lên từ https://www.python.org/downloads/
2. Cài đặt Python (nhớ chọn "Add Python to PATH")
3. Kiểm tra cài đặt:
```bash
python --version
# hoặc
python3 --version
```

## Bước 2: Tạo Virtual Environment (Khuyến nghị)

```bash
# Di chuyển vào thư mục rasa
cd rasa

# Tạo virtual environment
python -m venv venv

# Kích hoạt virtual environment
# Trên Windows:
venv\Scripts\activate
# Trên Linux/Mac:
source venv/bin/activate
```

## Bước 3: Cài đặt Rasa

```bash
# Cài đặt các package cần thiết
pip install -r requirements.txt

# Hoặc cài đặt trực tiếp
pip install rasa==3.6.0
pip install rasa-sdk==3.6.0
```

## Bước 4: Train Model

```bash
# Train model Rasa (tạo file model từ training data)
rasa train
```

Lệnh này sẽ:
- Đọc các file trong `data/nlu/`, `data/stories/`, `data/rules/`
- Train model và lưu vào thư mục `models/`
- Mất khoảng 1-5 phút tùy máy

## Bước 5: Chạy Rasa Server

```bash
# Chạy Rasa server với API và CORS
rasa run --enable-api --cors "*" --port 5005
```

Hoặc chạy với các tùy chọn khác:

```bash
# Chạy với debug mode
rasa run --enable-api --cors "*" --port 5005 --debug

# Chạy với logging chi tiết
rasa run --enable-api --cors "*" --port 5005 --log-file rasa.log
```

## Bước 6: Test Rasa

### Test trong shell:
```bash
rasa shell
```

### Test qua API:
```bash
# Test bằng curl
curl -X POST http://localhost:5005/webhooks/rest/webhook \
  -H "Content-Type: application/json" \
  -d '{"sender": "test_user", "message": "xin chào"}'
```

### Test qua Postman:
- URL: `http://localhost:5005/webhooks/rest/webhook`
- Method: POST
- Headers: `Content-Type: application/json`
- Body:
```json
{
  "sender": "test_user",
  "message": "xin chào"
}
```

## Bước 7: Cấu hình Laravel

Thêm vào file `.env`:

```env
RASA_URL=http://localhost:5005
RASA_PORT=5005
```

## Bước 8: Chạy song song

Bạn cần chạy 2 server cùng lúc:

1. **Laravel server** (terminal 1):
```bash
php artisan serve
```

2. **Rasa server** (terminal 2):
```bash
cd rasa
venv\Scripts\activate  # Windows
# hoặc
source venv/bin/activate  # Linux/Mac

rasa run --enable-api --cors "*" --port 5005
```

## Troubleshooting

### Lỗi: "rasa: command not found"
- Đảm bảo đã cài đặt Rasa: `pip install rasa`
- Kiểm tra virtual environment đã được kích hoạt chưa

### Lỗi: "Port 5005 already in use"
- Đổi port: `rasa run --enable-api --cors "*" --port 5006`
- Cập nhật `.env`: `RASA_URL=http://localhost:5006`

### Lỗi: "Model not found"
- Chạy `rasa train` để tạo model trước

### Lỗi kết nối từ Laravel
- Kiểm tra Rasa server đã chạy chưa
- Kiểm tra URL trong `.env` có đúng không
- Kiểm tra firewall có chặn port 5005 không

## Cải thiện Bot

1. **Thêm training data**: Sửa file `data/nlu/nlu.yml`
2. **Thêm stories**: Sửa file `data/stories/stories.yml`
3. **Thêm responses**: Sửa file `domain.yml`
4. **Train lại**: `rasa train`
5. **Restart server**: Dừng và chạy lại `rasa run`

## Lệnh hữu ích

```bash
# Train model
rasa train

# Test trong shell
rasa shell

# Xem cấu trúc project
rasa init --no-prompt  # (chỉ dùng lần đầu)

# Validate training data
rasa data validate

# Test stories
rasa test
```

## Chạy tự động khi khởi động máy (Windows)

Tạo file `start_rasa.bat` trong thư mục `rasa`:

```bat
@echo off
cd /d %~dp0
call venv\Scripts\activate
rasa run --enable-api --cors "*" --port 5005
pause
```

Double-click vào file này để chạy Rasa.

