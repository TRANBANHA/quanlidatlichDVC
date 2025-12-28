# 🚀 CÁCH CHẠY RASA CHATBOT - HƯỚNG DẪN ĐƠN GIẢN

## ⚡ CÁCH NHANH NHẤT

### Double-click vào file: `rasa/start_rasa.bat`

File này sẽ tự động:
- Tạo virtual environment
- Cài đặt Rasa  
- Train model
- Chạy server

---

## 📝 CÁCH CHẠY THỦ CÔNG

### Bước 1: Mở PowerShell

### Bước 2: Vào thư mục rasa

```powershell
cd C:\laragon\www\quanlidatlichyte\rasa
```

### Bước 3: Kích hoạt virtual environment

```powershell
venv\Scripts\activate
```

Bạn sẽ thấy `(venv)` ở đầu dòng lệnh.

### Bước 4: Cài đặt Rasa (chỉ lần đầu)

```powershell
pip install --upgrade pip
pip install rasa==3.6.0 rasa-sdk==3.6.0
```

**Lưu ý:** Nếu gặp lỗi với `psycopg2-binary`, bỏ qua vì không cần thiết.

### Bước 5: Train model (chỉ lần đầu)

```powershell
rasa train
```

Chờ khoảng 1-5 phút. Khi thấy:
```
Your Rasa model is trained and saved at 'models/...'
```
→ ✅ Đã train xong!

### Bước 6: Chạy Rasa server

```powershell
rasa run --enable-api --cors "*" --port 5005
```

Khi thấy:
```
Starting Rasa server on http://0.0.0.0:5005
```
→ ✅ Rasa đã chạy thành công!

---

## ✅ KIỂM TRA

Mở trình duyệt: http://localhost:5005/status

Nếu thấy JSON → ✅ Rasa đã chạy!

---

## ⚙️ CẤU HÌNH LARAVEL

Thêm vào `.env`:
```env
RASA_URL=http://localhost:5005
RASA_PORT=5005
```

---

## 🔄 CHẠY SONG SONG

Cần **2 terminal**:

**Terminal 1 - Laravel:**
```powershell
cd C:\laragon\www\quanlidatlichyte
php artisan serve
```

**Terminal 2 - Rasa:**
```powershell
cd C:\laragon\www\quanlidatlichyte\rasa
venv\Scripts\activate
rasa run --enable-api --cors "*" --port 5005
```

---

## 🐛 XỬ LÝ LỖI

**Lỗi: "rasa: command not found"**
→ Chưa kích hoạt venv hoặc chưa cài Rasa

**Lỗi: "Port 5005 already in use"**
→ Đổi port: `rasa run --enable-api --cors "*" --port 5006`

**Lỗi: "Model not found"**
→ Chạy `rasa train` trước

---

## 📚 TÓM TẮT LỆNH

```powershell
# 1. Vào thư mục
cd C:\laragon\www\quanlidatlichyte\rasa

# 2. Kích hoạt venv
venv\Scripts\activate

# 3. Train (lần đầu)
rasa train

# 4. Chạy server
rasa run --enable-api --cors "*" --port 5005
```

✅ **Xong! Rasa đã sẵn sàng!**

