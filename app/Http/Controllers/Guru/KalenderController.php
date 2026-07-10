<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
//Sama dengan Admin\KalenderController, namun ini untuk guru
class KalenderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|between:2000,2100',
            'month' => 'nullable|integer|between:1,12',
        ]);

        $year = $validated['year'] ?? (int) date('Y');
        $month = $validated['month'] ?? (int) date('m');

        $firstDay = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDayOfWeek = $firstDay->dayOfWeek;

        $monthEvents = CalendarEvent::whereYear('event_date', $year)
            ->whereMonth('event_date', $month)
            ->where(fn($q) => $q->where('scope', 'school')->orWhere('user_id', auth()->id()))
            ->orderBy('event_date')
            ->get();
        $events = $monthEvents->groupBy(fn($event) => $event->event_date->format('Y-m-d'));

        $prevMonth = $firstDay->copy()->subMonth();
        $nextMonth = $firstDay->copy()->addMonth();

        return view('guru.kalender', compact(
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
            'is_done' => 'boolean',
        ]);

        CalendarEvent::create($validated + [
            'user_id' => auth()->id(),
            'scope' => 'user',
            'is_holiday' => $request->boolean('is_holiday'),
            'is_done' => $request->boolean('is_done'),
        ]);

        return back()->with('success', 'Event berhasil ditambahkan.');
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        if ((int) $calendarEvent->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'is_holiday' => 'boolean',
            'is_done' => 'boolean',
        ]);

        $calendarEvent->update($validated + [
            'is_holiday' => $request->boolean('is_holiday'),
            'is_done' => $request->boolean('is_done'),
        ]);

        return back()->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        if ($calendarEvent->user_id !== auth()->id()) {
            abort(403);
        }
        $calendarEvent->delete();
        return back()->with('success', 'Event berhasil dihapus.');
    }
}
