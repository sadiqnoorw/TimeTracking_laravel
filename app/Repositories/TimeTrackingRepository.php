<?php
namespace App\Repositories;

use App\Models\TimeTrackingEntry;
use App\Repositories\Contracts\TimeTrackingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeTrackingRepository implements TimeTrackingRepositoryInterface
{
    public function startWork($employeeId, $workTypeId)
    {
        return TimeTrackingEntry::create([
            'user_id' => $employeeId,
            'type' => 'work',
            'start_time' => now(),
            'work_type_id' => $workTypeId,
        ]);
    }

    public function stopWork($employeeId)
    {
        $entry = TimeTrackingEntry::where('user_id', $employeeId)
            ->where('type', 'work')
            ->whereNull('end_time')
            ->latest()
            ->first();

        return $entry ? $entry->update(['end_time' => now()]) : false;
    }

    public function startBreak($employeeId, $reasonId)
    {
        return TimeTrackingEntry::create([
            'user_id' => $employeeId,
            'type' => 'break',
            'start_time' => now(),
            'break_reason_id' => $reasonId,
        ]);
    }

    public function stopBreak($employeeId)
    {
        $entry = TimeTrackingEntry::where('user_id', $employeeId)
            ->where('type', 'break')
            ->whereNull('end_time')
            ->latest()
            ->first();

        return $entry ? $entry->update(['end_time' => now()]) : false;
    }

    public function getEntriesByDate($employeeId, $date)
    {
        return TimeTrackingEntry::where('user_id', $employeeId)
            ->whereDate('start_time', $date)
            ->get();
    }

    public function getDailyReport($userId): Collection
    {
        // Get all entries for this user
        $entries = TimeTrackingEntry::where('user_id', $userId)
            ->orderBy('start_time', 'desc')
            ->get();

        // Group them by date and compute totals
        $dailyReport = $entries->groupBy(function($item) {
            return $item->start_time->format('Y-m-d');
        })->map(function($items) {
            $workTime = $items->where('type', 'work')->sum(function ($entry) {
                return $entry->end_time ? $entry->start_time->diffInMinutes($entry->end_time) : 0;
            });

            $breakTime = $items->where('type', 'break')->sum(function ($entry) {
                return $entry->end_time ? $entry->start_time->diffInMinutes($entry->end_time) : 0;
            });

            return [
                'entries'   => $items,
                'work_time' => $workTime,
                'break_time'=> $breakTime
            ];
        });

        return $dailyReport;
    }

    public function getCurrentStatus($userId)
    {
        $activeWork = TimeTrackingEntry::where('user_id', $userId)
            ->where('type', 'work')
            ->whereNull('end_time')
            ->first();

        $activeBreak = TimeTrackingEntry::where('user_id', $userId)
            ->where('type', 'break')
            ->whereNull('end_time')
            ->first();

        return [
            'activeWork'  => $activeWork ? true : false,
            'activeBreak' => $activeBreak ? true : false,
        ];
    }

}
