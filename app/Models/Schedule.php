<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'staff_record_id', 'created_by',
        'schedule_date', 'time_in', 'time_out', 'shift', 'notes',
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function staffRecord()
    {
        return $this->belongsTo(StaffRecord::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
