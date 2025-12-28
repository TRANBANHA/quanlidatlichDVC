# Rasa Chatbot cho Hệ thống Đặt lịch Dịch vụ Hành chính Công

## Cài đặt

### 1. Cài đặt Python và Rasa

```bash
# Cài đặt Python 3.8 trở lên
# Sau đó cài đặt Rasa
pip install -r requirements.txt
```

### 2. Train model

```bash
# Train model Rasa
rasa train
```

### 3. Chạy Rasa server

```bash
# Chạy Rasa server (port 5005)
rasa run --enable-api --cors "*"

# Hoặc chạy với shell để test
rasa shell
```

### 4. Cấu hình trong Laravel

Thêm vào file `.env`:

```env
RASA_URL=http://localhost:5005
RASA_PORT=5005
```

## Cấu trúc thư mục

- `config.yml`: Cấu hình pipeline và policies
- `domain.yml`: Định nghĩa intents, entities, responses
- `data/nlu/`: Training data cho NLU
- `data/stories/`: Training data cho stories
- `data/rules/`: Rules cho bot
- `credentials.yml`: Cấu hình credentials
- `endpoints.yml`: Cấu hình endpoints

## Cách hoạt động

1. Khi người dùng chat, nếu không có cán bộ online, Rasa sẽ trả lời tự động
2. Khi cán bộ vào room chat, Rasa sẽ dừng và cán bộ sẽ trả lời
3. Rasa được tích hợp qua `RasaService` trong Laravel

## Cải thiện bot

1. Thêm training data vào `data/nlu/nlu.yml`
2. Thêm stories vào `data/stories/stories.yml`
3. Thêm rules vào `data/rules/rules.yml`
4. Train lại: `rasa train`
5. Restart server: `rasa run --enable-api --cors "*"`

## Test

```bash
# Test trong shell
rasa shell

# Test API
curl -X POST http://localhost:5005/webhooks/rest/webhook \
  -H "Content-Type: application/json" \
  -d '{"sender": "test_user", "message": "xin chào"}'
```

