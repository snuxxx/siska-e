<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_request_id',
        'nama_dokumen',
        'path_dokumen',
    ];

    // Relasi ke LeaveRequest
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }
}
