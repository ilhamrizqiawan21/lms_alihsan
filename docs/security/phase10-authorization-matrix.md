# Phase 10 — Authorization Matrix & IDOR Audit

Branch: `experimental/demo-lms`

## Audit scope

This audit covers the HTTP routes currently defined in `routes/web.php` and the Phase 10 completion routes in `routes/phase10-security.php`, with special attention to routes containing route-model parameters.

Authorization is evaluated at three layers:

1. **Role boundary** — `auth` + `role:*` middleware.
2. **Resource ownership / relationship** — `can:*` middleware or explicit controller checks.
3. **Nested-resource integrity** — child records must belong to the parent resource supplied in the URL.

## Controller-by-controller matrix

| Controller / area | Role boundary | Resource authorization | Nested ownership | IDOR status |
|---|---|---|---|---|
| `Admin\\UserController` | `role:admin` | Admin-only route group; target user is admin-managed | N/A | PASS |
| `Admin\\KelasController` | `role:admin` | Admin-only | N/A | PASS |
| `Admin\\MataPelajaranController` | `role:admin` | Admin-only | N/A | PASS |
| `Admin\\KelasMapelController` | `role:admin` | Admin-only | N/A | PASS |
| `Admin\\KelasSiswaController` | `role:admin` | Admin-only | Student/class relationship is handled by controller validation | PASS |
| `Admin\\TahunAjaranController` | `role:admin` | Admin-only | N/A | PASS |
| `Admin\\PengumumanController` | Admin/guru/kepsek routes | Controller checks role and creator for mutation; target classes are constrained for guru | `kelas_mapel` targets constrained to guru-owned classes | PASS |
| `Admin\\KalenderController` | `role:admin` | Admin-only; school/user scope enforced by controller | Event update/delete ownership enforced | PASS |
| `Admin\\RekapController` | `role:admin` | Admin-only | Query-based reports | PASS |
| `Admin\\SchoolSettingController` | `role:admin` | Admin-only | N/A | PASS |
| `Admin\\SystemController` | `role:admin` | Admin-only | Blocked-IP record is admin-only | PASS |
| `Admin\\AcademicAuditLogController` | `role:admin` | Admin-only | N/A | PASS |
| `NotificationController` | Role-specific route group | `markRead()` requires `notifikasi.user_id === Auth::id()` | N/A | PASS |
| `Guru\\KelasMapelWorkspaceController` | `role:guru` | `can:mengajar,kelasMapel` | N/A | PASS |
| `Guru\\AbsensiController` | `role:guru` | `can:mengajar,kelasMapel` | Attendance data is scoped to authorized class subject | PASS |
| `Guru\\MateriController` | `role:guru` | `can:mengajar,kelasMapel` for parameterized routes | Material is checked against class-subject relationship | PASS |
| `Guru\\TugasController` | `role:guru` | `can:mengajar,kelasMapel`; task delete now also uses `can:mengajar-tugas,tugas` | Task, submission, student and file parent relationships are validated | PASS |
| `Guru\\NilaiController` | `role:guru` | `can:mengajar,kelasMapel` | Student/class relationship checked by controller | PASS |
| `Guru\\SikapController` | `role:guru` | `can:mengajar,kelasMapel` | Student/class relationship checked by controller | PASS |
| `Guru\\WaliKelasController` | `role:guru` | `can:kelola-wali-kelas,waliKelas` | Child records checked against parent `wali_kelas_id`; students checked against wali class | PASS |
| `Guru\\ChatController` | `role:guru` | `can:mengajar,kelasMapel` | Chat room is bound to authorized class-subject | PASS |
| `Guru\\NotifikasiController` | `role:guru` | Controller checks notification ownership | N/A | PASS |
| `Siswa\\KelasMapelWorkspaceController` | `role:siswa` | Controller scopes class-subject to student's class | N/A | PASS |
| `Siswa\\MateriController` | `role:siswa` | Controller requires active class-subject belonging to student's class | Material must belong to supplied class-subject | PASS |
| `Siswa\\TugasController` | `role:siswa` | Controller requires active task belonging to student's class | Submission/file must belong to supplied task and current student | PASS |
| `Siswa\\ChatController` | `role:siswa` | Controller requires active class-subject belonging to student's class | Chat messages scoped by class-subject | PASS |
| `Siswa\\NotifikasiController` | `role:siswa` | Controller checks notification ownership | N/A | PASS |
| `Siswa\\PengumumanController` | `role:siswa` | Controller filters by visibility/target | N/A | PASS |
| `Kepsek\\LaporanController` | `role:kepala_sekolah` | Wali-class detail uses `can:lihat-laporan-wali-kelas,waliKelas` | Wali class must be active | PASS |
| `Kepsek\\KalenderController` | `role:kepala_sekolah` | Monitoring-only; mutations explicitly return 403 | N/A | PASS |
| `Kepsek\\StatistikController` | `role:kepala_sekolah` | Kepsek-only | Query-based reports | PASS |

## Parameterized route review

### Guru

- `{kelasMapel}` routes for attendance, materials, tasks, grades, attitude, chat and workspace are protected by `can:mengajar,kelasMapel`.
- `{waliKelas}` routes are protected by `can:kelola-wali-kelas,waliKelas`.
- `{tugas}`, `{pengumpulan}`, `{file}`, `{siswa}`, `{pertemuan}`, and `{penanganan}` are additionally validated against their parent relationship inside the controller.
- The previously weaker task deletion route `/guru/tugas/{tugas}` has been upgraded with `can:mengajar-tugas,tugas`.

### Siswa

- `{kelasMapel}` is checked against the authenticated student's `kelas_id` and active status.
- `{tugas}` is checked against the authenticated student's class and active class-subject.
- `{file}` and `{pengumpulan}` are checked against both the supplied task and the authenticated student's own submission.
- `{pengumuman}` visibility is checked by role/target.
- `{notifikasi}` is checked against `Auth::id()`.

### Kepala Sekolah

- `{waliKelas}` report detail is protected by `lihat-laporan-wali-kelas`.
- Calendar mutation routes accept a model parameter but deliberately return HTTP 403 because the role is monitoring-only.
- `{pengumuman}` access/mutation is controlled by `PengumumanController` role/creator rules.
- `{notifikasi}` ownership is checked before marking a notification read.

### Admin

- All parameterized administrative resources remain inside `role:admin`.
- Notification access is additionally scoped to the authenticated user.
- Calendar mutation ownership/scope is enforced by the admin calendar controller.

## Findings and changes

### Finding A — task deletion had controller-only ownership enforcement

`Guru\\TugasController::destroy()` already called `authorize('mengajar', $tugas->kelasMapel)`, so the operation was not directly exploitable through the controller. However, the route itself had no explicit resource authorization, making the route map weaker than the rest of the task endpoints.

**Remediation:**

- Added `App\\Policies\\TugasPolicy`.
- Added `mengajar-tugas` Gate registration.
- Added `can:mengajar-tugas,tugas` to `guru.tugas.destroy`.
- Kept the controller authorization as defense-in-depth.

### Finding B — notification routes rely on controller ownership checks

This is intentional defense-in-depth: notification records are user-owned and both the generic notification controller and guru notification controller reject a notification whose `user_id` differs from `Auth::id()`.

No route-only policy was added because the same controller is shared by multiple role groups and the existing ownership check is explicit and role-independent.

## Remaining verification

The source audit is complete for the route files available in this branch. Runtime verification still needs to be executed in the repository environment with the application's database and test suite.

Minimum runtime IDOR test matrix:

- Guru A → Guru B's `KelasMapel` → 403.
- Guru A → Guru B's `Tugas` delete → 403.
- Guru A → Guru B's task submission/file → 403.
- Guru A → Guru B's `WaliKelas` → 403.
- Siswa A → Siswa B's submission/file → 403.
- Siswa A → another class's `KelasMapel`/task/material/chat → 403.
- Any role → another user's notification mark-read → 403.
- Kepala sekolah → any calendar mutation → 403.

## Conclusion

No unmitigated high-risk IDOR was identified in the audited parameterized routes. The most notable route-level gap, task deletion, has been hardened with an explicit resource Gate while preserving the existing controller-level authorization.
