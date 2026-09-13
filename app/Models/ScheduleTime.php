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

    public function getFormattedTimeAttribute(): string
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }
}
