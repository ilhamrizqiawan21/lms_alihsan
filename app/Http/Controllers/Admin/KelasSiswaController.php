<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use App\Services\SiswaImportService;
use App\Services\SiswaTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\UniqueConstraintViolationException;
use Inertia\Inertia;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderName;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\BorderWidth;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\CellVerticalAlignment;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class KelasSiswaController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::withCount(['siswa' => fn ($query) => $query->where('status', 'aktif')])
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
        //Urutan User berdasarkan NIS/Kode Guru
        $query = Siswa::with(['user', 'kelas'])
            ->whereHas('user')
            ->orderBy('nis');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        $siswa = $query->paginate(25)
            ->withQueryString()
            ->through(function (Siswa $siswa) {
                $passwordIsDefault = (bool) $siswa->user?->is_password_default;

                return [
                    'id' => $siswa->id,
                    'nis' => $siswa->nis,
                    'kelas_id' => $siswa->kelas_id,
                    'kelas' => trim(($siswa->kelas?->tingkat ? $siswa->kelas->tingkat . ' ' : '') . ($siswa->kelas?->nama_kelas ?? '')),
                    'status' => $siswa->status,
                    'tinggal_kelas' => (bool) $siswa->tinggal_kelas,
                    'nama_lengkap' => $siswa->user?->nama_lengkap,
                    'jenis_kelamin' => $siswa->user?->jenis_kelamin,
                    'password_is_default' => $passwordIsDefault,
                    'password_status' => $passwordIsDefault ? 'Masih default' : 'Sudah diubah',
                ];
            });

        return Inertia::render('Admin/KelasSiswa/Index', [
            'kelasList' => $kelasList->map(fn (Kelas $kelas) => [
                'id' => $kelas->id,
                'tingkat' => $kelas->tingkat,
                'nama_kelas' => $kelas->nama_kelas,
                'label' => "{$kelas->tingkat} {$kelas->nama_kelas}",
                'siswa_count' => $kelas->siswa_count,
            ]),
            'siswa' => $siswa,
            'filters' => $request->only(['kelas_id', 'search']),
            'importErrors' => session('import_errors', []),
            'studentPassword' => session('student_password'),
            'templateUrl' => route('admin.kelas-siswa.import.template'),
            'exportUrl' => route('admin.kelas-siswa.export.excel'),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'search' => 'nullable|string|max:100',
        ]);

        $query = Siswa::with(['user', 'kelas'])
            ->whereHas('user')
            ->orderBy('nis');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        $writer = new Writer();
        $filePath = tempnam(sys_get_temp_dir(), 'siswa_');
        $filename = 'status_password_siswa_' . date('Ymd_His') . '.xlsx';

        $writer->openToFile($filePath);
        $writer->getCurrentSheet()->setColumnWidth(22, 1);
        $writer->getCurrentSheet()->setColumnWidth(32, 2);
        $writer->getCurrentSheet()->setColumnWidth(18, 3);
        $writer->getCurrentSheet()->setColumnWidth(18, 4);
        $writer->getCurrentSheet()->setColumnWidth(24, 5);

        $styles = $this->excelStyles();
        $writer->addRow(Row::fromValuesWithStyle([school_setting('school_name', 'Nama Sekolah')], $styles['school'], 24));
        $writer->addRow(Row::fromValuesWithStyle(['STATUS PASSWORD SISWA'], $styles['title'], 24));
        $writer->addRow(Row::fromValuesWithStyle(['Tanggal Export', now()->format('d/m/Y H:i')], $styles['meta'], 18));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValuesWithStyle([
            'Username',
            'Nama',
            'Kelas',
            'Password Default',
            'Status Password',
        ], $styles['tableHeader'], 24));

        $query->get()->values()->each(function (Siswa $siswa, int $index) use ($writer, $styles) {
            $user = $siswa->user;
            $isDefaultPassword = (bool) $user?->is_password_default;

            $writer->addRow(Row::fromValuesWithStyle([
                $user?->username ?? '-',
                $user?->nama_lengkap ?? '-',
                trim(($siswa->kelas?->tingkat ? $siswa->kelas->tingkat . ' ' : '') . ($siswa->kelas?->nama_kelas ?? '')) ?: '-',
                User::DEFAULT_PASSWORD,
                $isDefaultPassword ? 'Masih default' : 'Sudah diubah',
            ], $index % 2 === 0 ? $styles['row'] : $styles['alternateRow'], 20));
        });

        $writer->close();

        return response()
            ->download($filePath, $filename)
            ->deleteFileAfterSend(true);
    }

    public function downloadTemplate(SiswaTemplateService $templateService)
    {
        return response()
            ->download($templateService->createTemplateFile(), SiswaTemplateService::FILENAME)
            ->deleteFileAfterSend(true);
    }

    public function importSiswa(Request $request, SiswaImportService $importService)
    {
        $request->validate([
            'file_siswa' => 'required|file|mimes:xlsx|max:5120',
        ]);

        $result = $importService->import($request->file('file_siswa')->getRealPath());

        if ($result['errors'] !== []) {
            return back()->with('import_errors', $result['errors']);
        }

        return back()->with('success', $result['imported'] . ' siswa berhasil diimport.');
    }

    //Save new Siswa
    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis|unique:users,username',
            'nama_lengkap' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
        ], [
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
        ]);

        try {
            $created = DB::transaction(function () use ($validated) {
                $siswaRoleId = Role::where('nama_role', 'siswa')->value('id');
                $password = $this->generateInitialPassword();

                if (!$siswaRoleId) {
                    throw new \RuntimeException('Role siswa belum tersedia.');
                }

                // Buat user dulu
                $user = User::create([
                    'username' => $validated['nis'],
                    'password' => Hash::make($password),
                    'is_password_default' => true,
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'role_id' => $siswaRoleId,
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'is_active' => true,
                ]);

                // Buat siswa
                Siswa::create([
                    'user_id' => $user->id,
                    'nis' => $validated['nis'],
                    'kelas_id' => $validated['kelas_id'],
                    'status' => 'aktif',
                ]);

                return compact('user', 'password');
            });

            return back()->with(
                'success',
                "Siswa {$validated['nama_lengkap']} berhasil ditambahkan."
            )->with('student_password', [
                'title' => 'Password awal siswa',
                'name' => $validated['nama_lengkap'],
                'username' => $created['user']->username,
                'password' => $created['password'],
            ]);
        } catch (UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->with('error', 'Data sudah ada di database. Silakan periksa kembali NIS atau username yang dimasukkan.');
        }
    }
    //Edit Siswa
    public function updateSiswa(Request $request, Siswa $siswa)
    {
        $userId = $siswa->user_id;

        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $siswa->id . '|unique:users,username,' . $userId,
            'nama_lengkap' => 'required|string|max:100',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tinggal_kelas' => 'boolean',
        ]);

        DB::transaction(function () use ($siswa, $validated) {
            $siswa->update([
                'nis' => $validated['nis'],
                'kelas_id' => $validated['kelas_id'],
                'tinggal_kelas' => $validated['tinggal_kelas'] ?? false,
            ]);

            $siswa->user->update([
                'username' => $validated['nis'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
            ]);
        });

        return back()->with('success', 'Data siswa berhasil diperbarui.');
    }
    //Reset password ke password default.
    public function resetPassword(Siswa $siswa)
    {
        $password = $this->generateInitialPassword();

        $siswa->user->update([
            'password' => Hash::make($password),
            'is_password_default' => true,
        ]);

        return back()
            ->with('success', 'Password siswa berhasil direset.')
            ->with('student_password', [
                'title' => 'Password baru siswa',
                'name' => $siswa->user->nama_lengkap,
                'username' => $siswa->user->username,
                'password' => $password,
            ]);
    }
    //Delete Siswa beserta Usernya
    public function destroySiswa(Siswa $siswa)
    {
        $nama = $siswa->user->nama_lengkap;
        $userId = $siswa->user_id;
        $siswa->delete();
        User::destroy($userId);
        return back()->with('success', "Siswa {$nama} berhasil dihapus.");
    }
    //Tampilkan daftar siswa yang sudah lulus
    public function luluskanKelas(Kelas $kelas)
    {
        if ($kelas->tingkat !== 'IX') {
            return back()->with('error', 'Hanya kelas IX yang bisa diluluskan.');
        }

        $count = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->update(['status' => 'lulus']);

        return back()->with('success', "{$count} siswa kelas {$kelas->nama_kelas} berhasil diluluskan.");
    }

    private function generateInitialPassword(): string
    {
        return User::DEFAULT_PASSWORD;
    }

    private function excelStyles(): array
    {
        $border = new Border(
            new BorderPart(BorderName::TOP, 'CBD5E1', BorderWidth::THIN),
            new BorderPart(BorderName::RIGHT, 'CBD5E1', BorderWidth::THIN),
            new BorderPart(BorderName::BOTTOM, 'CBD5E1', BorderWidth::THIN),
            new BorderPart(BorderName::LEFT, 'CBD5E1', BorderWidth::THIN),
        );

        $base = (new Style())
            ->withFontName('Arial')
            ->withFontSize(10)
            ->withShouldWrapText(true)
            ->withCellVerticalAlignment(CellVerticalAlignment::CENTER);

        return [
            'school' => $base
                ->withFontBold(true)
                ->withFontSize(14)
                ->withFontColor('0F172A')
                ->withCellAlignment(CellAlignment::CENTER),
            'title' => $base
                ->withFontBold(true)
                ->withFontSize(13)
                ->withFontColor(Color::WHITE)
                ->withBackgroundColor('1D4ED8')
                ->withCellAlignment(CellAlignment::CENTER),
            'meta' => $base
                ->withFontColor('475569')
                ->withBackgroundColor('F8FAFC'),
            'tableHeader' => $base
                ->withFontBold(true)
                ->withFontColor(Color::WHITE)
                ->withBackgroundColor('334155')
                ->withCellAlignment(CellAlignment::CENTER)
                ->withBorder($border),
            'row' => $base
                ->withBackgroundColor(Color::WHITE)
                ->withBorder($border),
            'alternateRow' => $base
                ->withBackgroundColor('F8FAFC')
                ->withBorder($border),
        ];
    }
}
