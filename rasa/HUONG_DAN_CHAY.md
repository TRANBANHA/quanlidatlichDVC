# HƯỚNG DẪN CHẠY RASA CHATBOT

## ⚡ CÁCH NHANH NHẤT (Windows)

### Double-click vào file: `rasa/start_rasa.bat`

Script sẽ tự động:
- Tạo virtual environment (nếu chưa có)
- Cài đặt Rasa (nếu chưa có)  
- Train model (nếu chưa có)
- Chạy Rasa server

---

## 📝 CÁCH CHẠY THỦ CÔNG

### Bước 1: Mở PowerShell hoặc CMD

### Bước 2: Di chuyển vào thư mục rasa

```powershell
cd C:\laragon\www\quanlidatlichyte\rasa
```

### Bước 3: Tạo virtual environment (chỉ lần đầu)

```powershell
python -m venv venv
```

### Bước 4: Kích hoạt virtual environment

```powershell
venv\Scripts\activate
```

Sau khi kích hoạt, bạn sẽ thấy `(venv)` ở đầu dòng lệnh.

### Bước 5: Cài đặt Rasa (chỉ lần đầu)

```powershell
pip install -r requirements.txt
```

Hoặc:
```powershell
pip install rasa==3.6.0 rasa-sdk==3.6.0
```

### Bước 6: Train model (chỉ lần đầu, hoặc khi sửa training data)

```powershell
rasa train
```

Quá trình này mất khoảng 1-5 phút. Khi xong sẽ có thông báo:
```
Your Rasa model is trained and saved at 'models/...'
```

### Bước 7: Chạy Rasa server

```powershell
rasa run --enable-api --cors "*" --port 5005
```

Khi thấy dòng này → Rasa đã chạy thành công:
```
Starting Rasa server on http://0.0.0.0:5005
```

---

## ✅ KIỂM TRA RASA ĐÃ CHẠY

Mở trình duyệt và truy cập:
```
http://localhost:5005/status
```

Nếu thấy JSON response → ✅ Rasa đã chạy thành công!

---

## 🧪 TEST RASA

### Test trong shell:
Mở terminal mới (giữ terminal đang chạy Rasa):
```powershell
cd C:\laragon\www\quanlidatlichyte\rasa
venv\Scripts\activate
rasa shell
```

Gõ "xin chào" và xem Rasa trả lời.

### Test qua API:
Truy cập: http://localhost:5005/docs để xem API documentation

---

## ⚙️ CẤU HÌNH LARAVEL

Thêm vào file `.env` (nếu chưa có):

```env
RASA_URL=http://localhost:5005
RASA_PORT=5005
```

---

## 🔄 CHẠY SONG SONG

Bạn cần chạy **2 server** cùng lúc:

### Terminal 1: Laravel
```powershell
cd C:\laragon\www\quanlidatlichyte
php artisan serve
```

### Terminal 2: Rasa
```powershell
cd C:\laragon\www\quanlidatlichyte\rasa
venv\Scripts\activate
rasa run --enable-api --cors "*" --port 5005
```

---

## 🐛 XỬ LÝ LỖI

### Lỗi: "rasa: command not found"
**Giải pháp:**
```powershell
# Đảm bảo đã kích hoạt virtual environment
venv\Scripts\activate

# Cài đặt lại Rasa
pip install rasa==3.6.0
```

### Lỗi: "Port 5005 already in use"
**Giải pháp:**
```powershell
# Đổi port
rasa run --enable-api --cors "*" --port 5006
```

Cập nhật `.env`:
```env
RASA_URL=http://localhost:5006
RASA_PORT=5006
```

### Lỗi: "Model not found"
**Giải pháp:**
```powershell
rasa train
```

### Lỗi: "ModuleNotFoundError: No module named 'rasa'"
**Giải pháp:**
```powershell
# Kích hoạt virtual environment
venv\Scripts\activate

# Cài đặt lại
pip install -r requirements.txt
```

---

## 📚 CẢI THIỆN BOT

1. **Sửa training data**: File `data/nlu/nlu.yml`
2. **Sửa stories**: File `data/stories/stories.yml`
3. **Sửa responses**: File `domain.yml`
4. **Train lại**:
   ```powershell
   rasa train
   ```
5. **Restart server**: Dừng (Ctrl+C) và chạy lại:
   ```powershell
   rasa run --enable-api --cors "*" --port 5005
   ```

---

## 💡 TIPS

- **Giữ terminal Rasa luôn mở** khi test chat
- **Train lại model** mỗi khi sửa training data
- **Kiểm tra log** nếu có lỗi: `rasa run --enable-api --cors "*" --port 5005 --debug`
- **Test nhanh**: Dùng `rasa shell` để test trước khi tích hợp vào Laravel

---

## 🎯 TÓM TẮT LỆNH

```powershell
# 1. Vào thư mục
cd C:\laragon\www\quanlidatlichyte\rasa

# 2. Kích hoạt venv
venv\Scripts\activate

# 3. Train (lần đầu hoặc sau khi sửa data)
rasa train

# 4. Chạy server
rasa run --enable-api --cors "*" --port 5005
```

✅ **Xong! Rasa đã sẵn sàng chat!**

