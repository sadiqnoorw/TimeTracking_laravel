<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TimeTrackingService;
use App\Models\BreakReason;
use App\Models\WorkType;


class TimeTrackingController extends Controller
{
    protected $service;

    public function __construct(TimeTrackingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $breakReasons = BreakReason::all();
        $workTypes = WorkType::all();
        $status = $this->service->getCurrentStatus(auth()->id());
        // dd($status);
        return view('time_tracking.index', compact('workTypes', 'breakReasons', 'status'));
    }

    public function startWork(Request $request)
{
    $this->service->startWork(auth()->id(), $request->work_type_id);
    return response()->json([
        'message' => 'Work started successfully.',
        'action'  => 'disable_start_work'
    ]);
}

public function stopWork()
{
    $this->service->stopWork(auth()->id());
    return response()->json([
        'message' => 'Work stopped successfully.',
        'action'  => 'disable_stop_work'
    ]);
}

public function startBreak(Request $request)
{
    $this->service->startBreak(auth()->id(), $request->break_reason_id);
    return response()->json([
        'message' => 'Break started successfully.',
        'action'  => 'disable_start_break'
    ]);
}

public function stopBreak()
{
    $this->service->stopBreak(auth()->id());
    return response()->json([
        'message' => 'Break stopped successfully.',
        'action'  => 'disable_stop_break'
    ]);
}

    public function report()
    {
        $dailyReport = $this->service->getDailyReport(auth()->id());
        return view('time_tracking.report', compact('dailyReport'));
    }

    
}
