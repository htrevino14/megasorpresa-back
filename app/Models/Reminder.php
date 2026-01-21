<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_name',
        'date',
        'notify_days_before',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'notify_days_before' => 'integer',
        ];
    }

    /**
     * Get the user that owns the reminder.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification date.
     */
    public function getNotificationDateAttribute(): \Carbon\Carbon
    {
        return $this->date->copy()->subDays($this->notify_days_before);
    }

    /**
     * Check if the reminder should be sent.
     */
    public function shouldNotify(): bool
    {
        $notificationDate = $this->date->copy()->subDays($this->notify_days_before);
        return $notificationDate->isToday() || $notificationDate->isPast();
    }
}
