<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\KelasMapel;
use App\Models\Pengumuman;
use App\Models\Tugas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarTimelineService
{
    public function forUser(User $user, int $year, int $month): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth()->endOfDay();

        return collect()
            ->merge($this->calendarEvents($user, $start, $end))
            ->merge($this->tasks($user, $start, $end))
            ->merge($this->announcements($user, $start, $end))
            ->sortBy(fn (array $item) => [$item['date'], $item['priority'], $item['title']])
            ->values();
    }

    private function calendarEvents(User $user, Carbon $start, Carbon $end): Collection
    {
        $events = CalendarEvent::whereBetween('event_date', [$start->toDateString(), $end->toDateString()])
            ->where(fn ($q) => $q->where('scope', 'school')->orWhere('user_id', $user->id))
            ->orderBy('event_date')
            ->get();

        return $events->map(fn (CalendarEvent $event) => [
            'id' => 'calendar-'.$event->id,
            'source_id' => $event->id,
            'type' => 'calendar',
            'type_label' => $event->is_holiday ? 'Hari Libur' : 'Event',
            'title' => $event->title,
            'description' => $event->description,
            'date' => $event->event_date->format('Y-m-d'),
            'date_label' => $event->event_date->translatedFormat('d F Y'),
            'time_label' => null,
            'is_done' => (bool) $event->is_done,
            'is_holiday' => (bool) $event->is_holiday,
            'scope' => $event->scope,
            'priority' => 20,
            'detail_url' => null,
            'can_manage' => (int) $event->user_id === (int) $user->id,
        ]);
    }

    private function tasks(User $user, Carbon $start, Carbon $end): Collection
    {
        $query = Tugas::with(['kelasMapel.kelas', 'kelasMapel.mataPelajaran', 'kelasMapel.guru'])
            ->whereBetween('batas_waktu', [$start, $end]);

        if ($user->isGuru()) {
            $query->whereHas('kelasMapel', fn ($q) => $q->where('guru_id', $user->id));
        } elseif ($user->isSiswa()) {
            $kelasId = $user->siswa?->kelas_id;
            if (!$kelasId) {
                return collect();
            }
            $query->whereHas('kelasMapel', fn ($q) => $q->where('kelas_id', $kelasId));
        }

        return $query->orderBy('batas_waktu')->get()->map(function (Tugas $task) use ($user) {
            $mapel = $task->kelasMapel?->mataPelajaran?->nama_mapel ?? '-';
            $kelas = $task->kelasMapel?->kelas?->nama_kelas ?? '-';
            $deadline = $task->batas_waktu;
            $detailUrl = null;

            if ($user->isSiswa()) {
                $detailUrl = route('siswa.tugas.show', $task);
            } elseif ($user->isGuru()) {
                $detailUrl = route('guru.tugas.pengumpulan', [$task->kelas_mapel_id, $task->id]);
            }

            return [
                'id' => 'task-'.$task->id,
                'source_id' => $task->id,
                'type' => 'task',
                'type_label' => 'Deadline Tugas',
                'title' => $task->judul,
                'description' => $task->deskripsi,
                'date' => $deadline?->format('Y-m-d'),
                'date_label' => $deadline?->translatedFormat('d F Y') ?? '-',
                'time_label' => $deadline?->format('H:i'),
                'is_done' => false,
                'is_holiday' => false,
                'scope' => 'academic',
                'priority' => 10,
                'detail_url' => $detailUrl,
                'can_manage' => $user->isGuru(),
                'meta' => trim($mapel.' · '.$kelas, ' ·'),
            ];
        });
    }

    private function announcements(User $user, Carbon $start, Carbon $end): Collection
    {
        $query = Pengumuman::with(['creator', 'kelasMapel.kelas', 'kelasMapel.mataPelajaran'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at');

        if ($user->isGuru()) {
            $query->where(function ($q) use ($user) {
                $q->whereIn('target', ['semua', 'guru'])
                    ->orWhere('created_by', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        $q->where('target', 'kelas_mapel')
                            ->whereIn('kelas_mapel_id', KelasMapel::where('guru_id', $user->id)->select('id'));
                    });
            });
        } elseif ($user->isSiswa()) {
            $kelasId = $user->siswa?->kelas_id;
            $query->where(function ($q) use ($kelasId) {
                $q->whereIn('target', ['semua', 'siswa'])
                    ->orWhere(function ($q) use ($kelasId) {
                        $q->where('target', 'kelas_mapel')
                            ->where(function ($q) use ($kelasId) {
                                $q->whereIn('kelas_mapel_id', KelasMapel::where('kelas_id', $kelasId)->select('id'))
                                    ->orWhere('target_kelas', 'like', '%"'.$kelasId.'"%');
                            });
                    });
            });
        } elseif ($user->isKepalaSekolah()) {
            $query->whereIn('target', ['semua', 'guru'])
                ->orWhere('created_by', $user->id);
        }

        return $query->get()->map(fn (Pengumuman $item) => [
            'id' => 'announcement-'.$item->id,
            'source_id' => $item->id,
            'type' => 'announcement',
            'type_label' => 'Pengumuman',
            'title' => $item->judul,
            'description' => $item->isi,
            'date' => Carbon::parse($item->created_at)->format('Y-m-d'),
            'date_label' => Carbon::parse($item->created_at)->translatedFormat('d F Y'),
            'time_label' => Carbon::parse($item->created_at)->format('H:i'),
            'is_done' => false,
            'is_holiday' => false,
            'scope' => $item->target,
            'priority' => 30,
            'detail_url' => $this->announcementUrl($user, $item),
            'can_manage' => false,
            'meta' => $item->creator?->nama_lengkap ?? '-',
        ]);
    }

    private function announcementUrl(User $user, Pengumuman $item): string
    {
        return match ($user->role?->nama_role) {
            'siswa' => route('siswa.pengumuman.show', $item),
            'guru' => route('guru.pengumuman.show', $item),
            'kepala_sekolah' => route('kepsek.pengumuman.show', $item),
            default => route('admin.pengumuman.show', $item),
        };
    }
}
