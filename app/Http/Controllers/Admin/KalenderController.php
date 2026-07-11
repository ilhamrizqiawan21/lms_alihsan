<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        return view('admin.kalender', compact(
            'events', 'year', 'month', 'daysInMonth',
            'startDayOfWeek', 'prevMonth', 'nextMonth', 'firstDay', 'monthEvents'
        ));
    }
    //Input Event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'is_holiday' => 'boolean',
            'scope' => 'required|in:user,school',
            'is_done' => 'boolean',
        ]);

        $validated['is_holiday'] = $request->boolean('is_holiday');
        $validated['is_done'] = $request->boolean('is_done');

        CalendarEvent::create($validated + ['user_id' => auth()->id()]);

        return back()->with('success', 'Event berhasil ditambahkan.');
    }
    //Update Event
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
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
    //Delete Event
    public function destroy(CalendarEvent $calendarEvent)
    {
        $calendarEvent->delete();
        return back()->with('success', 'Event berhasil dihapus.');
    }
}
