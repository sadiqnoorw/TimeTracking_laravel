<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeTrackingEntry extends Model
{
    protected $fillable = [
        'user_id', 'type', 'start_time', 'end_time',
        'break_reason_id', 'work_type_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakReason()
    {
        return $this->belongsTo(BreakReason::class);
    }

    public function workType()
    {
        return $this->belongsTo(WorkType::class);
    }

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];
}
