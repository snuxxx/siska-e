<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'jenis_pengajuan',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'dokumen_id',
        'status',
        'is_urgent',
    ];

    // Relasi ke Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveBalance()
{
    return $this->belongsTo(LeaveBalance::class, 'employee_id', 'employee_id');
}


    // Relasi ke LeaveDocuments
    public function document()
    {
        return $this->belongsTo(LeaveDocument::class, 'dokumen_id');
    }

}
