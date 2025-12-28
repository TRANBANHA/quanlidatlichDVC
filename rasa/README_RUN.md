# 🚀 HƯỚNG DẪN CHẠY RASA - ĐƠN GIẢN NHẤT

## ⚡ CÁCH CHẠY NHANH NHẤT

### **Double-click vào file: `rasa/RUN_RASA.bat`**

File này sẽ **TỰ ĐỘNG**:
- ✅ Kiểm tra Python
- ✅ Tạo virtual environment (nếu chưa có)
- ✅ Cài đặt Rasa (nếu chưa có)
- ✅ Train model (nếu chưa có)
- ✅ Chạy Rasa server trên port 5005

**Không cần làm gì thêm, chỉ cần double-click!**

---

## 📋 YÊU CẦU

- **Python 3.8+** đã được cài đặt
- Kết nối Internet (để cài đặt packages)

---

## 🎯 SAU KHI CHẠY

1. **Kiểm tra Rasa đã chạy:**
   - Mở trình duyệt: http://localhost:5005/status
   - Nếu thấy JSON → ✅ Rasa đã chạy!

2. **Sử dụng trên website:**
   - Mở website Laravel
   - Click vào chat
   - Chọn phường
   - Gửi tin nhắn → Rasa sẽ tự động trả lời

---

## ⚠️ LƯU Ý

- **Giữ cửa sổ CMD mở** khi Rasa đang chạy
- **Đóng cửa sổ CMD** để dừng Rasa server
- **Cần chạy cả Laravel server** (nếu chưa chạy)

---

## 🐛 XỬ LÝ LỖI

### Lỗi: "Python chưa được cài đặt"
→ Cài đặt Python từ: https://www.python.org/downloads/
→ Nhớ tick "Add Python to PATH" khi cài đặt

### Lỗi: "Port 5005 already in use"
→ Port đã bị chiếm bởi ứng dụng khác
→ Đóng ứng dụng đang dùng port 5005 hoặc đổi port trong file `.env`

### Lỗi khi cài đặt Rasa
→ Chạy lại file `RUN_RASA.bat` một lần nữa
→ Hoặc cài đặt thủ công: `pip install rasa==3.6.0`

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề, kiểm tra:
1. Python đã được cài đặt chưa?
2. Đã kích hoạt virtual environment chưa?
3. Port 5005 có bị chiếm không?
4. File `config.yml` và `data/` có đúng không?

---

✅ **Chỉ cần double-click `RUN_RASA.bat` là xong!**

