<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffRecord extends Model
{
    // Columns that can be filled by mass assignment
    protected $fillable = [
        'user_id',
        'employee_id',
        'position',
        'department',
        'branch',
        'hire_date',
        'salary',
        'status',
        'notes',
    ];

    // A staff record belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
