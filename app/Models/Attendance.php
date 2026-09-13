<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'time',
        'status',
        'latitude',
        'longitude',
        'photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'present' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'late' => 'bg-amber-100 text-amber-700 border-amber-200',
            'sick' => 'bg-blue-100 text-blue-700 border-blue-200',
            'permission' => 'bg-purple-100 text-purple-700 border-purple-200',
            'absent' => 'bg-rose-100 text-rose-700 border-rose-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'present' => 'Hadir Tepat Waktu',
            'late' => 'Terlambat',
            'sick' => 'Sakit',
            'permission' => 'Izin',
            'absent' => 'Alpa / Tanpa Keterangan',
            default => ucfirst($this->status),
        };
    }
}
