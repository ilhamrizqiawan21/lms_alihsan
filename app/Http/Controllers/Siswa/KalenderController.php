<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;

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
            ->orderBy('event_date')->get();
        $events = $monthEvents->groupBy(fn($event) => $event->event_date->format('Y-m-d'));

        $prevMonth = $firstDay->copy()->subMonth();
        $nextMonth = $firstDay->copy()->addMonth();

        return view('siswa.kalender', compact('events','year','month','daysInMonth','startDayOfWeek','prevMonth','nextMonth','firstDay','monthEvents'));
    }
}
