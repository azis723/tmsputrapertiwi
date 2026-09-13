<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleTime extends Model
{
    use HasFactory;

    protected $table = 'schedules_times';

    protected $fillable = [
        'day',
        'start_time',
        'end_time',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'schedule_time_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw("CASE day 
            WHEN 'Monday' THEN 1 
            WHEN 'Tuesday' THEN 2 
            WHEN 'Wednesday' THEN 3 
            WHEN 'Thursday' THEN 4 
            WHEN 'Friday' THEN 5 
            WHEN 'Saturday' THEN 6 
            ELSE 7 END")
            ->orderBy('start_time');
    }

    public function getDayLabelAttribute(): string
    {
        return match($this->day) {
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            default => $this->day,
        };
    }

    public function getFormattedTimeAttribute(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }
}
