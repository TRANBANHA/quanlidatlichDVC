# 🚀 HƯỚNG DẪN CHẠY RASA CHATBOT

## ⚡ CÁCH NHANH NHẤT (KHUYẾN NGHỊ)

### Double-click vào file: `rasa/start_rasa.bat`

File này sẽ tự động:
- ✅ Kiểm tra Python
- ✅ Cài đặt Rasa (nếu chưa có)
- ✅ Train model (nếu chưa có)
- ✅ Chạy Rasa server trên port 5005

---

## 📝 CÁCH CHẠY THỦ CÔNG

### Bước 1: Mở Terminal (PowerShell hoặc Git Bash)

**Nếu dùng PowerShell:**
```powershell
cd D:\laragon\www\quanlidatliyte\rasa
```

**Nếu dùng Git Bash:**
```bash
cd /d/laragon/www/quanlidatliyte/rasa
```

### Bước 2: Tạo virtual environment (chỉ lần đầu, nếu chưa có)

Nếu chưa có thư mục `venv`, tạo mới:

**Nếu dùng PowerShell:**
```powershell
python -m venv venv
```

**Nếu dùng Git Bash:**
```bash
python -m venv venv
```

### Bước 3: Kích hoạt virtual environment

**Nếu dùng PowerShell:**

⚠️ **QUAN TRỌNG:** PowerShell thường chặn chạy scripts. Bạn **PHẢI** chạy lệnh này trước:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
```

Sau đó mới kích hoạt venv:
```powershell
.\venv\Scripts\activate
```

**Hoặc chạy gộp một lệnh:**
```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass; .\venv\Scripts\activate
```

> 💡 **Lưu ý:** Lệnh `Set-ExecutionPolicy` chỉ áp dụng cho cửa sổ PowerShell hiện tại, không ảnh hưởng đến hệ thống. Mỗi lần mở PowerShell mới, bạn cần chạy lại lệnh này.

**Nếu dùng Git Bash:**
```bash
source venv/Scripts/activate
```

Bạn sẽ thấy `(venv)` ở đầu dòng lệnh.

### Bước 4: Cài đặt Rasa (chỉ lần đầu)

```bash
pip install --upgrade pip
pip install rasa==3.6.0 rasa-sdk==3.6.0
```

**Lưu ý:** Nếu gặp lỗi với `psycopg2-binary`, bỏ qua vì không cần thiết.

### Bước 5: Train model (chỉ lần đầu)

```bash
rasa train
```

Chờ khoảng 1-5 phút. Khi thấy:
```
Your Rasa model is trained and saved at 'models/...'
```
→ ✅ Đã train xong!

### Bước 6: Chạy Rasa server

```bash
rasa run --enable-api --cors "*" --port 5005
```

Khi thấy:
```
Starting Rasa server on http://0.0.0.0:5005
```
→ ✅ Rasa đã chạy thành công!

---

## ✅ KIỂM TRA RASA ĐÃ CHẠY

Mở trình duyệt: **http://localhost:5005/status**

Nếu thấy JSON response → ✅ Rasa đã chạy!

---

## ⚙️ CẤU HÌNH LARAVEL

Đảm bảo file `.env` có:

```env
RASA_URL=http://localhost:5005
RASA_PORT=5005
```

---

## 🔄 CHẠY SONG SONG

Cần **2 terminal**:

**Terminal 1 - Laravel:**
```bash
cd D:\laragon\www\quanlidatliyte
php artisan serve
```

**Terminal 2 - Rasa (PowerShell):**
```powershell
cd D:\laragon\www\quanlidatliyte\rasa
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\venv\Scripts\activate
rasa run --enable-api --cors "*" --port 5005
```

**Terminal 2 - Rasa (Git Bash):**
```bash
cd /d/laragon/www/quanlidatliyte/rasa
source venv/Scripts/activate
rasa run --enable-api --cors "*" --port 5005
```

---

## 🐛 XỬ LÝ LỖI

### Lỗi: "rasa: command not found"
→ Chưa kích hoạt venv hoặc chưa cài Rasa
→ Giải pháp (PowerShell):
```powershell
.\venv\Scripts\activate
pip install -r requirements.txt
```
→ Giải pháp (Git Bash):
```bash
source venv/Scripts/activate
pip install -r requirements.txt
```

### Lỗi: "Port 5005 already in use"
→ Port đã bị chiếm
→ Giải pháp: Đổi port
```powershell
rasa run --enable-api --cors "*" --port 5006
```
Và cập nhật `.env`:
```env
RASA_URL=http://localhost:5006
RASA_PORT=5006
```

### Lỗi: "Model not found"
→ Chưa train model
→ Giải pháp:
```powershell
rasa train
```

### Lỗi: "ModuleNotFoundError: No module named 'rasa'"
→ Chưa kích hoạt virtual environment
→ Giải pháp (PowerShell):
```powershell
.\venv\Scripts\activate
pip install -r requirements.txt
```
→ Giải pháp (Git Bash):
```bash
source venv/Scripts/activate
pip install -r requirements.txt
```

---

## 📚 TÓM TẮT LỆNH

**PowerShell:**
```powershell
# 1. Vào thư mục
cd D:\laragon\www\quanlidatliyte\rasa

# 2. Tạo venv (chỉ lần đầu, nếu chưa có)
python -m venv venv

# 3. Bỏ qua execution policy (QUAN TRỌNG - phải chạy trước khi activate)
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass

# 4. Kích hoạt venv
.\venv\Scripts\activate

# 5. Cài đặt Rasa (chỉ lần đầu)
pip install --upgrade pip
pip install -r requirements.txt

# 6. Train (lần đầu)
rasa train

# 7. Chạy server
rasa run --enable-api --cors "*" --port 5005
```

**Git Bash:**
```bash
# 1. Vào thư mục
cd /d/laragon/www/quanlidatliyte/rasa

# 2. Tạo venv (chỉ lần đầu, nếu chưa có)
python -m venv venv

# 3. Kích hoạt venv
source venv/Scripts/activate

# 4. Cài đặt Rasa (chỉ lần đầu)
pip install --upgrade pip
pip install -r requirements.txt

# 5. Train (lần đầu)
rasa train

# 6. Chạy server
rasa run --enable-api --cors "*" --port 5005
```

---

## 🎯 CÁCH SỬ DỤNG

1. **Chạy Rasa server** (theo một trong các cách trên)
2. **Chạy Laravel server** (nếu chưa chạy)
3. **Mở website** và click vào chat
4. **Chọn phường** khi được hỏi
5. **Gửi tin nhắn** → Rasa sẽ tự động trả lời (nếu chưa có cán bộ)
6. **Khi cán bộ vào room** → Rasa sẽ dừng, chỉ cán bộ chat

---

## ⚠️ LƯU Ý QUAN TRỌNG

- ✅ **Giữ terminal/PowerShell mở** khi Rasa đang chạy
- ✅ **Cần chạy cả Laravel và Rasa** cùng lúc
- ✅ **Kiểm tra port 5005** không bị chiếm bởi ứng dụng khác
- ✅ **Nếu cán bộ vào room trước** → Rasa sẽ dừng và chỉ cán bộ chat

---

## 🧪 TEST RASA

Sau khi Rasa chạy, test bằng cách:

1. **Test trong shell:**
```bash
rasa shell
```
Gõ "xin chào" và xem Rasa trả lời.

2. **Test qua API:**
```bash
curl -X POST http://localhost:5005/webhooks/rest/webhook -H "Content-Type: application/json" -d '{"sender":"test","message":"xin chào"}'
```

3. **Test trên website:**
- Mở website
- Click chat
- Chọn phường
- Gửi "xin chào"
- Xem Rasa có trả lời không

---

✅ **Xong! Rasa đã sẵn sàng chat!**

