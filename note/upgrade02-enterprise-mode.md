# Upgrade 02 — Enterprise Mode (song song với Classic)

> **Nguyên tắc tối thượng:** Classic hiện tại giữ nguyên, không đụng. Enterprise là ver mới bật qua flag/toggle, chạy song song.
> **Admin bá đạo** của user giữ nguyên — `session('is_admin')` vẫn return early mọi nơi.

Trạng thái: **đang chờ user chốt open questions** (xem mục 7). Mai trả lời rồi mới viết plan chi tiết + code.

---

## 1. Mục tiêu

User muốn thêm 1 chế độ "Enterprise" cho phép:
- Lập **Công ty** → **Phòng ban** → gán nhân sự.
- Khi tạo task có thể chọn trong **3 cấp scope**: `Công ty — Phòng ban — Cá nhân`.
- Giữ role **Member / Lead / Manager** như hiện tại.
- Classic mode (cách làm việc hôm nay) vẫn chạy nguyên vẹn song song.
- Tài khoản admin vẫn bá đạo như cũ.

---

## 2. Hiện trạng có sẵn (không phải làm từ 0)

- Bảng `companies`, `branches`, `departments`, `team_members` đã có.
- `team_members.level` ∈ `manager | lead | member` + `parent_id` đã chạy — `visibleMemberIds()` dựa trên đó.
- Role/Permission system có sẵn (`Role`, `Permission`, `role_permission`).
- `session('is_admin')` giữ quyền bá đạo, `scopeVisible()` return early với admin.
- `Task` đã có cột `department_id`.
- `TaskController::scopeVisible()` hiện lọc theo assignee + creator thuộc visible set.

→ **Thiếu đúng 2 thứ:** scope "Công ty" cho task + flag/cột tách luồng classic/enterprise.

---

## 3. Kiến trúc đề xuất (nháp — chờ chốt)

### 3.1 Tách classic / enterprise bằng cột task

- Thêm `tasks.scope` enum `personal | department | company`, **nullable**, mặc định `null`.
- `scope = null` → task classic → `scopeVisible()` chạy nhánh cũ y hệt hôm nay.
- `scope ∈ {personal, department, company}` → task enterprise → `scopeVisible()` rẽ nhánh mới.
- UI tạo task có radio **"Chế độ"**: `Classic (như cũ)` / `Enterprise`. Chọn Enterprise mới hiện ô 3 cấp.
- Thêm `tasks.company_id` nullable (chỉ dùng khi `scope = company`).

### 3.2 Visibility matrix (chờ chốt)

| Scope task | Member thường | Lead (phòng của task) | Manager công ty | Admin |
|---|---|---|---|---|
| `null` (classic) | assignee + creator (như hôm nay) | theo `visibleMemberIds()` | theo `visibleMemberIds()` | ✅ |
| `personal` | chỉ assignee + creator | — | — | ✅ |
| `department` | member cùng `department_id` | ✅ | ✅ (nếu phòng thuộc công ty mình) | ✅ |
| `company` | member cùng `company_id` | ✅ | ✅ | ✅ |

### 3.3 Role tại từng cấp — 2 lựa chọn

- **(a) Đơn giản:** giữ `team_members.level` global. 1 người là `member`/`lead`/`manager` ở mức toàn cục.
  - Ưu: tận dụng code hiện có, không migration thêm.
  - Nhược: 1 người không thể Lead ở phòng A nhưng Member ở phòng B.
- **(b) Phức tạp:** bảng mới `department_member(department_id, member_id, role)` kiểu nhiều-nhiều có role riêng từng phòng.
  - Ưu: linh hoạt đúng mô hình doanh nghiệp thật.
  - Nhược: viết lại `visibleMemberIds()`, UI gán phức tạp.

→ **Đề xuất chọn (a) cho upgrade 02**, để dành (b) cho upgrade sau nếu phát sinh nhu cầu thật.

### 3.4 Admin bá đạo

- Không đụng gì. `session('is_admin')` vẫn trả về sớm ở `scopeVisible()` và các permission check.
- Admin thấy tất cả scope, tạo được task mọi scope, mọi công ty.

### 3.5 UI tạo task (enterprise mode)

```
Chế độ: ( ) Classic   ( ) Enterprise
└─ Khi chọn Enterprise:
   Scope:  ( ) Cá nhân   ( ) Phòng ban   ( ) Công ty
   ├─ personal:    chỉ hiện field Assignee (như classic rút gọn)
   ├─ department:  hiện Phòng ban dropdown (lọc theo công ty của user)
   └─ company:     hiện Công ty dropdown (admin thấy tất cả, manager chỉ thấy công ty mình)
```

Classic (default) → form y hệt hôm nay, không rối người quen tay.

---

## 4. Nguyên tắc "không đụng classic"

| Rule | Áp dụng |
|---|---|
| Không rename / drop cột cũ | Chỉ thêm `scope`, `company_id` nullable |
| `scope = null` → render & scope như cũ 100% | Mặc định tạo task vẫn `scope = null` |
| Route cũ không đổi URL/handler | Enterprise dùng cùng route, phân nhánh trong controller |
| Form tạo task mặc định ở tab Classic | User cũ không bị bất ngờ |
| Admin flow không đổi | Tất cả nơi check `is_admin` giữ nguyên |

---

## 5. Thứ tự thực hiện (nháp — sau khi chốt)

1. Migration thêm `tasks.scope`, `tasks.company_id` nullable.
2. Mở rộng `TaskController::scopeVisible()` thêm 3 nhánh `personal/department/company`. Giữ nhánh `null` nguyên.
3. Form `create.blade.php`: radio Classic/Enterprise, hiện field tương ứng.
4. `TaskController::store()`: validate theo mode, lưu `scope` + `company_id`.
5. Hiển thị badge scope trên task card (chỉ hiện khi `scope != null`).
6. Trang quản trị Enterprise (nếu cần — chờ chốt câu 5 ở mục 7).
7. Test cô lập cho từng scope (sqlite in-memory).

Mỗi bước commit riêng, không gộp.

---

## 6. Rủi ro & giảm thiểu

| Rủi ro | Mitigation |
|---|---|
| Classic user mở form tạo task thấy UI mới rối | Mặc định tab Classic, Enterprise phải chủ động chọn |
| `scope=company` lộ task cho người không nên thấy | Enforce `company_id` phải match company của viewer; admin bypass |
| Manager phòng A gán task cho người phòng B | Validate assignee thuộc visible set theo scope |
| Data cũ (scope null) bị treat nhầm là enterprise | Mọi `scopeVisible` check phải `whereNull('scope')` trước khi rẽ nhánh |
| 1 user thuộc nhiều công ty (nếu lựa chọn (a)) | Giới hạn 1 user = 1 branch = 1 company; không hỗ trợ multi-company ở upgrade này |

---

## 7. Open questions — mai user chốt

1. **Mode toggle:** per-task (chọn mỗi lần tạo) hay per-user setting ("tao luôn làm việc enterprise")?
   - *Đề xuất: per-task, 1 bảng cùng chứa cả 2 loại.*
2. **Role nhiều cấp (3.3):** chọn **(a) global level đơn giản** hay **(b) bảng nhiều-nhiều mỗi phòng 1 role**?
   - *Đề xuất: (a).*
3. **1 user thuộc nhiều phòng ban** có xảy ra trong thực tế không? Nếu không → càng chắc chọn (a).
4. **Scope `personal`** có cần không? Hay 2 cấp "công ty / phòng ban" đủ, cá nhân = logic assignee cũ?
5. **Trang quản trị Enterprise:** làm trang mới `/enterprise/manage` (chọn công ty → phòng ban → gán nhân sự + set level) hay tận dụng `/teams` + `/departments` hiện có?
6. **Khi admin tạo task `scope=company`:** mặc định lấy công ty đầu tiên trong DB, hay bắt chọn trong dropdown?
7. **Tên hiển thị mode:** "Classic / Enterprise" hay tiếng Việt "Cổ điển / Doanh nghiệp" hay gì khác?
8. **Default khi migrate task cũ:** để `scope = null` (classic) toàn bộ — xác nhận đúng ý?

---

## 8. Liên quan upgrade khác

- Upgrade 01 (department view + mention) không bị block bởi cái này — có thể làm trước, nhưng khi làm part B (notification) nên cân nhắc để `@all` scope theo context enterprise (`@all` trong task company = toàn công ty, trong task department = toàn phòng).
- Nếu user chốt làm Upgrade 02 trước → Upgrade 01 part B sẽ ăn theo scope mới dễ hơn.
