# Upgrade 01 — Hiển thị việc theo phòng ban + Tag mọi người

> Nguyên tắc tối thượng: **KHÔNG đụng luồng classic hiện tại.**
> Mọi thứ thêm vào phải opt-in bằng query param / toggle / route riêng.
> User đang dùng `/tasks/list`, `/tasks/board`... như cũ phải thấy y nguyên.

---

## 1. Hiện trạng

### 1.1 Việc theo phòng ban — CHƯA có
- `Task` đã có cột `department_id` (dùng ở create/edit/show).
- Các view `board / list / timeline / calendar` **không có filter / group-by** theo phòng ban.
- Scope hiện tại ở `TaskController::scopeVisible()` chỉ lọc theo assignee/creator thuộc visible set — không rẽ theo phòng.
- Trang `/departments` chỉ là danh mục phòng ban, không list task của phòng.

### 1.2 Tag mọi người — CÓ MỘT NỬA
- `@mention` cá nhân trong comment đã chạy (`resources/views/tasks/show.blade.php` — hàm `commentBox`, popup gợi ý khi gõ `@`).
- Chỉ chèn text `@tên` vào body comment. **Không lưu DB**, **không notify** ai cả.
- Chưa có `@all` (toàn bộ người thấy task) và `@phòng-ban` (toàn phòng).

---

## 2. Phạm vi upgrade này

Gom 2 việc liên quan nhau:
- **A.** Hiển thị việc theo phòng ban (filter + group-by).
- **B.** Tag mọi người (mở rộng @mention + notification tối thiểu).

**Ngoài phạm vi (phase sau):** email notification, realtime (websocket), audit log mention, @role.

---

## 3. Nguyên tắc "không đụng classic"

| Rule | Áp dụng |
|---|---|
| Không đổi hành vi mặc định của route cũ | `/tasks/list` không param → render y hệt hôm nay |
| Không đổi schema breaking | Chỉ thêm bảng/cột mới, không rename/drop |
| Không đổi chữ ký method cũ | Chỉ thêm method/param optional |
| View mới nằm partial riêng, include có điều kiện | `@if ($groupBy === 'department') @include(...) @endif` |
| Mention mở rộng là JS tăng cường | Không có JS mới thì popup @ cũ vẫn chạy |
| Notification bật được tắt được | Config `features.notifications` mặc định `true`, tắt là biến mất khỏi header |

---

## 4. Phần A — Việc theo phòng ban

### 4.1 Filter theo phòng ban

**Áp cho:** `tasks/list`, `tasks/board`, `tasks/calendar`, `tasks/timeline`, `tasks/archive`.

**Cách:**
- Thêm query param `?department=<id>` (và `?department=none` cho task chưa gán phòng, `?department=all` = mặc định cũ).
- `TaskController` thêm helper `applyDepartmentFilter($q, $request)` — gọi sau `scopeVisible()`.
- Không có param → không áp filter → **y hệt cũ**.
- UI: 1 dropdown nhỏ ở thanh toolbar đã có sẵn trong `_view_switcher.blade.php` (hoặc riêng từng view nếu switcher không gánh). Mặc định "Tất cả phòng ban".

**File đụng:**
- `app/Http/Controllers/TaskController.php` — thêm private method + gọi ở 5 action.
- `resources/views/tasks/_view_switcher.blade.php` — thêm dropdown.
- Không migration.

### 4.2 Group-by theo phòng (chỉ cho `list`)

**Cách:**
- Thêm query param `?group=department` (và `?group=status`, `?group=assignee` cho tương lai).
- Không có param → render flat như cũ.
- Có param → render partial `tasks/_list_grouped.blade.php` (tạo mới).

**File đụng:**
- `app/Http/Controllers/TaskController.php::list()` — nhận param, truyền `$groupBy` xuống view.
- `resources/views/tasks/list.blade.php` — bọc block hiện có bằng `@if ($groupBy)` / `@else` để không đổi layout mặc định.
- Partial mới: `resources/views/tasks/_list_grouped.blade.php`.

### 4.3 (Tùy chọn) Trang tổng quan "Việc theo phòng ban"

Chỉ làm nếu A.1 + A.2 chưa đủ.
- Route mới: `GET /tasks/by-department` → render view mới với các card phòng ban, mỗi card là mini-list task.
- **KHÔNG** sửa route cũ.

→ **Đề xuất: skip A.3 ở upgrade này.** Có filter + group-by là đủ dùng.

---

## 5. Phần B — Tag mọi người

### 5.1 Mở rộng popup `@` trong comment

**Trong `show.blade.php` — hàm `commentBox`:**
- Giữ nguyên logic cũ.
- Nguồn gợi ý `members` → đổi thành `mentionables`:
  - `{ type: 'all', label: '@all', hint: 'Tất cả người thấy task' }`
  - `{ type: 'department', id, label: '@phòng-X', hint: 'N thành viên' }`
  - `{ type: 'user', id, label: '@tên', hint: 'phòng ban / chức vụ' }`
- Render phân nhóm trong popup (All → Phòng → Cá nhân).
- Khi chọn → chèn text theo format `@all` / `@dept:slug-phòng` / `@user:tên`.

### 5.2 Lưu mention vào DB

**Bảng mới `comment_mentions`:**
```
id            bigint pk
comment_id    fk comments.id (cascade delete)
target_type   enum('all','department','user')
target_id     unsigned bigint nullable  -- null khi target_type='all'
created_at    timestamp
```

**`CommentController::store`** — sau khi tạo comment:
- Parse body tìm các token `@all`, `@dept:…`, `@user:…` → upsert vào `comment_mentions`.
- Parse là server-side, không tin client.

### 5.3 Render highlight khi hiển thị comment

- Trong view `show.blade.php` phần list comment: filter body qua helper `App\Support\MentionRenderer::toHtml($body, $mentions)` để biến token thành `<span class="mention">`.
- Giữ nguyên markdown cũ (bold/italic/code).

### 5.4 Notification tối thiểu

**Bảng mới `notifications`:**
```
id            bigint pk
member_id     fk team_members.id
type          string          -- 'mention.user' | 'mention.department' | 'mention.all'
task_id       fk tasks.id nullable
comment_id    fk comments.id nullable
payload       json nullable   -- actor_name, snippet
read_at       timestamp nullable
created_at    timestamp
```

**Dispatch:** sau khi lưu `comment_mentions`, resolve ra danh sách `member_id` cụ thể (expand `all`/`department`) rồi bulk insert `notifications`.
- `all` = `TaskOwnership::visibleMembersOf($task)`.
- `department` = members của phòng.
- Dedupe: 1 member chỉ nhận 1 notification/comment.
- **KHÔNG** notify chính người viết comment.

**UI tối thiểu:**
- Chuông ở header: badge số unread, dropdown 10 cái mới nhất, click → mở task.
- Trang `/notifications` list đầy đủ + "mark all read".
- Route mới, controller mới. **KHÔNG** đụng header hiện có — chèn component vào `layouts/app.blade.php` dưới dạng `@include('components.notifications-bell')` có `@if (session('member_id'))` guard.

### 5.5 Phần B — Fallback khi JS tắt

- Popup @ là JS tăng cường. User gõ tay `@all` / `@dept:ten-phong` / `@user:ten` vẫn hoạt động vì parse là server-side.
- Format cũ `@tên` thì vẫn giữ cho tương thích ngược — coi như `user` lookup theo tên.

---

## 6. Thứ tự thực hiện

1. **A.1 — Filter phòng ban** (nửa buổi, giá trị thấy liền, zero rủi ro).
2. **A.2 — Group-by phòng trong list** (~1h).
3. **B.1 — Mở rộng popup `@`** (frontend only, chưa lưu DB).
4. **B.2 + B.3 — Lưu mention + render** (migration + parser).
5. **B.4 — Notification bell + trang `/notifications`** (phần nặng nhất, làm cuối).

Mỗi bước xong → test riêng → commit riêng. Không gộp.

---

## 7. Test & Verify

- **A.1 / A.2:** mở lại `/tasks/list` và `/tasks/board` không param → so sánh pixel-level với trước upgrade. Phải y hệt.
- **B.1:** gõ `@` trong comment ở task cũ → popup mới hiện, nhưng chọn 1 user cá nhân vẫn ra format cũ `@tên` (không break).
- **B.2+:** tạo comment có `@all`, `@dept:xxx`, kiểm tra bảng `comment_mentions` và `notifications` đúng dữ liệu.
- **B.4:** login member thuộc phòng được tag → thấy badge; member khác không thấy.
- Test cô lập bằng sqlite in-memory cho phần parse mention.

---

## 8. Rủi ro & cách giảm

| Rủi ro | Mitigation |
|---|---|
| Filter phòng ban làm vỡ phân trang/sort | Dùng query builder cộng dồn, không viết lại query |
| Mention expand ra quá nhiều member → spam notification | Giới hạn `@all` chỉ với task có ≤ N người (config); hoặc rate-limit 1 notification/task/user/5 phút |
| Người viết tự tag mình | Lọc actor khỏi recipients |
| Classic user không hiểu notification mới | Mặc định bật nhưng icon chuông ẩn khi không có notification nào → không rối mắt |

---

## 9. Quyết định cần user chốt trước khi code

1. **Phần A trước, Phần B sau** — hay làm song song?
2. **A.3 (trang `by-department` riêng)** — làm hay skip? (đề xuất: skip)
3. **Notification channel** — chỉ in-app, hay cần email luôn? (đề xuất: chỉ in-app ở upgrade này)
4. **`@all` scope** — "mọi người thấy task" hay "mọi người trong hệ thống"? (đề xuất: scope theo task)
5. Dropdown filter đặt ở `_view_switcher` chung hay mỗi view tự có?
