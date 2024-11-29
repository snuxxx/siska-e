<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'requester_id',
        'requested_to_id',
        'status',
        'alasan',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requester_id');
    }

    public function requestedTo()
    {
        return $this->belongsTo(Employee::class, 'requested_to_id');
    }
}
