<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UrgentLeaveRequestNotification extends Notification
{
    use Queueable;

    private $leaveRequest;

    public function __construct($leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pengajuan Izin Mendadak')
            ->line('Karyawan telah mengajukan izin mendadak.')
            ->line('Jenis Pengajuan: ' . $this->leaveRequest->jenis_pengajuan)
            ->line('Tanggal: ' . $this->leaveRequest->tanggal_mulai . ' - ' . $this->leaveRequest->tanggal_selesai)
            ->action('Lihat Detail', url('/leave-requests/' . $this->leaveRequest->id))
            ->line('Mohon segera proses pengajuan ini.');
    }

    public function toArray($notifiable)
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'jenis_pengajuan' => $this->leaveRequest->jenis_pengajuan,
            'tanggal_mulai' => $this->leaveRequest->tanggal_mulai,
            'tanggal_selesai' => $this->leaveRequest->tanggal_selesai,
        ];
    }
}
