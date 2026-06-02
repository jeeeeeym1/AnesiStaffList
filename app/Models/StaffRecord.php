<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffRecord extends Model
{
    protected $fillable = [
        'user_id', 'employee_id', 'position', 'department',
        'branch', 'hire_date', 'salary', 'status', 'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
