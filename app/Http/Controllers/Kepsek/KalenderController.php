<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
//Kalender untuk Kepala Sekolah, menampilkan kalender dengan event sekolah dan event milik kepala sekolah, serta fitur CRUD untuk event
class KalenderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year = (int) ($validated['year'] ?? date('Y'));
        $month = (int) ($validated['month'] ?? date('m'));

        $firstDay = Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;

        // Kepsek mengelola event sekolah saja.
        $events = CalendarEvent::where('scope', 'school')
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->orderBy('event_date')
            ->get()
            ->groupBy(fn($e) => $e->event_date->format('Y-m-d'));

        $monthEvents = CalendarEvent::where('scope', 'school')
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->orderBy('event_date')
            ->get();

        $prevMonth = $firstDay->copy()->subMonth();
        $nextMonth = $firstDay->copy()->addMonth();

        return view('kepsek.kalender', compact(
            'events', 'year', 'month', 'daysInMonth',
            'startDayOfWeek', 'prevMonth', 'nextMonth', 'firstDay', 'monthEvents'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'is_holiday' => 'boolean',
            'scope' => 'required|in:school',
            'is_done' => 'boolean',
        ]);

        $validated['is_holiday'] = $request->boolean('is_holiday');
        $validated['is_done'] = $request->boolean('is_done');

        CalendarEvent::create($validated + ['user_id' => auth()->id()]);

        return back()->with('success', 'Event berhasil ditambahkan.');
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $this->authorizeSchoolEvent($calendarEvent);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'is_holiday' => 'boolean',
            'is_done' => 'boolean',
        ]);

        $validated['is_holiday'] = $request->boolean('is_holiday');
        $validated['is_done'] = $request->boolean('is_done');

        $calendarEvent->update($validated);

        return back()->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $this->authorizeSchoolEvent($calendarEvent);

        $calendarEvent->delete();
        return back()->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Toggle status selesai event.
     */
    public function toggleDone(CalendarEvent $calendarEvent)
    {
        $this->authorizeSchoolEvent($calendarEvent);

        $calendarEvent->update(['is_done' => !$calendarEvent->is_done]);

        return back()->with('success', $calendarEvent->is_done
            ? 'Event ditandai selesai.'
            : 'Event dibuka kembali.');
    }

    private function authorizeSchoolEvent(CalendarEvent $calendarEvent): void
    {
        abort_unless($calendarEvent->scope === 'school', 403);
    }
}
