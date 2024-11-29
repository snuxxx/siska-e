<?php

namespace App\Notifications;

use App\Models\ShiftRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftRequestNotification extends Notification
{
    use Queueable;

    public $shiftRequest;

    public function __construct(ShiftRequest $shiftRequest)
    {
        $this->shiftRequest = $shiftRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; // Menggunakan mail dan database untuk notifikasi
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Permintaan pertukaran shift telah diajukan.')
            ->line('Shift yang diminta: ' . $this->shiftRequest->shift->tanggal_shift)
            ->line('Alasan: ' . $this->shiftRequest->alasan)
            ->action('Lihat Permintaan', url('/shift-requests/' . $this->shiftRequest->id));
    }

    public function toDatabase($notifiable)
{
    if ($notifiable->id === $this->shiftRequest->requested_to_id) {
        return [
            'shift_request_id' => $this->shiftRequest->id,
            'message' => $this->shiftRequest->requester->name . ' mengajukan pertukaran shift dengan Anda.',
            'alasan' => $this->shiftRequest->alasan
        ];
    } else {
        return [
            'shift_request_id' => $this->shiftRequest->id,
            'message' => $this->shiftRequest->requester->name . ' mengajukan pertukaran shift.',
            'alasan' => $this->shiftRequest->alasan
        ];
    }
}


}
