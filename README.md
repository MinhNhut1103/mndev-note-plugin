# Mndev Plugin - Code Notes

**Plugin WordPress quản lý ghi chú code nội bộ** dành cho các nhà phát triển. Cho phép tạo, chỉnh sửa, xóa và quản lý các ghi chú về code, tính năng và tài liệu kỹ thuật trực tiếp từ WordPress admin.

---

## Tính năng nổi bật

### 📝 Quản lý ghi chú đầy đủ
- **Thêm ghi chú**: Tạo ghi chú mới với tiêu đề và nội dung dễ dàng
- **Chỉnh sửa ghi chú**: Nhấp vào nút Edit để cập nhật ghi chú hiện có
- **Xóa ghi chú**: Xóa ghi chú không cần thiết với xác nhận an toàn
- **Làm mới**: Tải lại danh sách ghi chú với một cú nhấp chuột

### 🎨 Giao diện hiện đại
- **Card notes**: Ghi chú hiển thị dạng card đẹp mắt với hiệu ứng hover
- **Form sidebar**: Form thêm/sửa ghi chú nằm ở sidebar tiện lợi
- **Hiệu ứng động**: Fade-in, slide-in, stagger animation cho danh sách ghi chú
- **Responsive**: Tối ưu hiển thị trên mọi kích thước màn hình (desktop, tablet, mobile)
- **Chế độ chỉnh sửa**: Highlight ghi chú đang được chỉnh sửa

### ⚡ AJAX Real-time
- **Thao tác không tải lại trang**: Thêm, sửa, xóa ghi chú qua AJAX
- **Phản hồi tức thì**: Hiển thị thông báo thành công/lỗi ngay lập tức
- **Animation khi xóa**: Hiệu ứng trượt ra khi xóa ghi chú
- **Highlight cập nhật**: Ghi chú được cập nhật sẽ được tô sáng trong 2 giây

### 🔐 Bảo mật
- **Phân quyền**: Chỉ người dùng có quyền `manage_options` mới có thể truy cập
- **Nonce validation**: Tất cả request AJAX đều được kiểm tra nonce
- **Sanitize dữ liệu**: Tiêu đề và nội dung được làm sạch trước khi lưu

### ⌨️ Shortcuts & Tiện ích
- **Ctrl+S**: Lưu ghi chú nhanh bằng phím tắt
- **Escape**: Hủy chỉnh sửa và đóng form
- **Auto-save**: Tự động lưu nháp (có thể kích hoạt tùy chỉnh)
- **Định dạng ngày tháng**: Hiển thị thời gian tương đối (vừa xong, X phút trước, X giờ trước)

### 🌐 Quốc tế hóa
- **Hỗ trợ đa ngôn ngữ**: Sử dụng WordPress text domain để dễ dàng dịch thuật
- **Tiếng Việt**: Giao diện đã được tối ưu cho tiếng Việt

---

## Yêu cầu hệ thống

- **WordPress**: 5.0 trở lên
- **PHP**: 7.0 trở lên
- **MySQL**: 5.6 trở lên

## Cài đặt

1. Tải plugin về thư mục `/wp-content/plugins/`
2. Kích hoạt plugin trong mục **Plugins** của WordPress admin
3. Truy cập **Mndev Notes** trong menu admin để bắt đầu sử dụng

## Cách sử dụng

### Thêm ghi chú mới
1. Nhập **tiêu đề** vào ô Title
2. Nhập **nội dung** vào ô Content
3. Nhấn **Add Note** hoặc **Ctrl+S** để lưu

### Chỉnh sửa ghi chú
1. Nhấp vào nút **Edit** trên ghi chú muốn sửa
2. Form sẽ tự động điền tiêu đề và nội dung
3. Chỉnh sửa nội dung và nhấn **Update Note**
4. Nhấn **Cancel** hoặc **Escape** để hủy chỉnh sửa

### Xóa ghi chú
1. Nhấp vào nút **Delete** trên ghi chú muốn xóa
2. Xác nhận xóa trong hộp thoại
3. Ghi chú sẽ được xóa với hiệu ứng animation

---

## Cấu trúc cơ sở dữ liệu

Khi kích hoạt, plugin tạo bảng `wp_mndev_notes` với cấu trúc:

| Cột        | Kiểu           | Mô tả                |
|------------|----------------|----------------------|
| id         | mediumint(9)   | ID tự động tăng      |
| title      | varchar(255)   | Tiêu đề ghi chú      |
| content    | text           | Nội dung ghi chú     |
| created_at | datetime       | Thời gian tạo        |
| updated_at | datetime       | Thời gian cập nhật   |

---

## Tác giả

- **Tác giả**: Minh Nhựt (Mndev)
- **Website**: [https://dominhnhut.com/](https://dominhnhut.com/)
- **GitHub**: [MinhNhut1103](https://github.com/MinhNhut1103)

## Giấy phép

Phát hành dưới giấy phép GPL v2 hoặc later. Xem file [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) để biết thêm chi tiết.
