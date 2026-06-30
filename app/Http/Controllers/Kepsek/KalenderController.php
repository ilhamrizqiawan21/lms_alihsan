<?php

namespace App\Http\Controllers\Kepsek;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

class KalenderController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;

        // Kepsek bisa lihat semua event (school + milik sendiri)
        $events = CalendarEvent::whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->orderBy('event_date')
            ->get()
            ->groupBy(fn($e) => $e->event_date->format('Y-m-d'));

        $monthEvents = CalendarEvent::whereYear('event_date', $year)
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

        CalendarEvent::create($validated + ['user_id' => auth()->id()]);

        return back()->with('success', 'Event berhasil ditambahkan.');
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'is_holiday' => 'boolean',
            'is_done' => 'boolean',
        ]);

        $calendarEvent->update($validated);

        return back()->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $calendarEvent->delete();
        return back()->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Toggle status selesai event.
     */
    public function toggleDone(CalendarEvent $calendarEvent)
    {
        $calendarEvent->update(['is_done' => !$calendarEvent->is_done]);

        return back()->with('success', $calendarEvent->is_done
            ? 'Event ditandai selesai.'
            : 'Event dibuka kembali.');
    }
}