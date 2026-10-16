# Hệ thống quản lý lớp học

Ứng dụng Laravel 11 quản lý lớp học, sinh viên, menu và người dùng, có đăng nhập và phân quyền theo hai nhóm: **Quản trị viên** và **Giảng viên**.

## Cài đặt

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # dùng SQLite mặc định
php artisan migrate --seed            # tạo bảng + dữ liệu mẫu
php artisan serve
```

Mở http://127.0.0.1:8000 và đăng nhập bằng tài khoản mẫu (mật khẩu đều là `password`):

| Vai trò | Email |
|---|---|
| Quản trị viên | `admin@example.com` |
| Giảng viên | `giangvien@example.com` |

Chạy test: `php artisan test`

## Chức năng và phân quyền

| Chức năng | Đường dẫn | Admin | Giảng viên |
|---|---|:---:|:---:|
| Dashboard | `/` | ✓ | ✓ |
| Quản lý lớp học | `/lophoc` | ✓ tất cả lớp | ✓ lớp mình chủ nhiệm |
| Quản lý sinh viên | `/sinhvien` | ✓ tất cả sinh viên | ✓ sinh viên lớp mình chủ nhiệm |
| Tài khoản của tôi | `/taikhoan` | ✓ | ✓ |
| Quản lý menu | `/menu` | ✓ | ✗ (403) |
| Quản lý người dùng | `/nguoidung` | ✓ | ✗ (403) |

- Các trang danh sách đều có phân trang (10/25/50 dòng), bộ lọc và sắp xếp theo cột.
- Form thêm/sửa được validate bằng các lớp trong `app/Http/Requests`.
- Sidebar lấy từ bảng `menus`. Menu đánh dấu "Chỉ admin" sẽ bị ẩn với giảng viên.
- Admin không thể tự xóa, tự khóa hoặc tự hạ quyền tài khoản của chính mình.
- Mỗi lớp có một giảng viên chủ nhiệm (`lop_hocs.giang_vien_id`). Admin chọn giảng viên khi tạo/sửa lớp; giảng viên tạo lớp thì lớp tự thuộc về mình.
- Giảng viên chỉ thấy, thêm, sửa lớp mình chủ nhiệm và sinh viên của các lớp đó (web, API và Dashboard). Truy cập lớp/sinh viên của người khác trả về 404.
- Chỉ admin được xóa lớp (giảng viên gọi xóa sẽ nhận 403, nút Xóa lớp bị ẩn). Giảng viên vẫn được xóa sinh viên trong lớp mình.
- Khi thêm/sửa sinh viên, giảng viên bắt buộc chọn một lớp mình chủ nhiệm.

## Middleware

| Alias | Lớp | Tác dụng |
|---|---|---|
| `role` | `CheckRole` | Chỉ cho các vai trò được liệt kê, vd `role:admin` |
| `tai.khoan.hoat.dong` | `CheckTaiKhoanHoatDong` | Tài khoản bị khóa sẽ bị đăng xuất / chặn API |
| `gio.hanh.chinh` | `CheckGioHanhChinh` | API lớp học, sinh viên chỉ dùng được 8h–17h |

## API

Mọi phản hồi có dạng `{ "success": bool, "message": string, "data": ... }`. Danh sách có thêm `meta` (phân trang).

1. Lấy token: `POST /api/login` với `email`, `password`.
2. Gửi kèm header `Authorization: Bearer <token>` và `Accept: application/json` cho các API còn lại.

| Method | Endpoint | Mô tả |
|---|---|---|
| POST | `/api/login` | Đăng nhập, trả token |
| GET | `/api/user` | Thông tin tài khoản đang đăng nhập |
| POST | `/api/logout` | Thu hồi token |
| GET | `/api/lop-hoc` | Danh sách lớp: `search`, `trang_thai`, `sort_by`, `sort_dir`, `per_page`, `page` |
| POST | `/api/lop-hoc` | Thêm lớp |
| GET | `/api/lop-hoc/{id}` | Chi tiết lớp (kèm số sinh viên) |
| PUT | `/api/lop-hoc/{id}` | Cập nhật lớp |
| DELETE | `/api/lop-hoc/{id}` | Xóa lớp (chỉ admin) |
| GET | `/api/sinh-vien` | Danh sách sinh viên: `search`, `lop_hoc_id`, `gioi_tinh`, `trang_thai`, `sort_by`, `sort_dir`, `per_page`, `page` |
| POST | `/api/sinh-vien` | Thêm sinh viên |
| GET | `/api/sinh-vien/{id}` | Chi tiết sinh viên (kèm lớp) |
| PUT | `/api/sinh-vien/{id}` | Cập nhật sinh viên |
| DELETE | `/api/sinh-vien/{id}` | Xóa sinh viên |

Các API lớp học / sinh viên chỉ hoạt động trong giờ hành chính (8h–17h, giờ Việt Nam). Ngoài giờ sẽ trả về lỗi 400.

## Dữ liệu mẫu

- Migration: `database/migrations`
- Factory: `UserFactory`, `LopHocFactory`, `SinhVienFactory`, `MenuFactory`
- Seeder: `UserSeeder` (admin, giảng viên + giảng viên ngẫu nhiên), `LopHocSeeder`, `SinhVienSeeder`, `MenuSeeder`. `DatabaseSeeder` còn dùng factory sinh thêm 5 lớp, mỗi lớp 4 sinh viên.
