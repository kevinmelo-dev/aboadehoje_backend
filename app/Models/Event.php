<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'title',
        'about',
        'local',
        'date',
        'time',
        'price',
        'category',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Registra a data do evento no formato aceito pelo banco de dados
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    // Retorna a data do evento no formato padrão brasileiro
    public function getDateAttribute(): string
    {
        return Carbon::createFromFormat('Y-m-d', $this->attributes['date'])->format('d/m/Y');
    }

    // Registra a hora do evento no formato aceito pelo banco de dados
    public function setTimeAttribute($value)
    {
        $this->attributes['time'] = Carbon::createFromFormat('H:i', $value)->format('H:i:00');
    }

    // Retorna o horário do evento sem os segundos
    public function getTimeAttribute(): string
    {
        return Carbon::createFromFormat('H:i:s', $this->attributes['time'])->format('H:i');
    }

    public function nextEvents()
    {
        $today = Carbon::now()->format('Y-m-d');
        return $this->query()->orderBy('date', 'ASC')->where('date', '>=', $today)->get();
    }

    public function todayEvents()
    {
        $today = Carbon::now()->format('Y-m-d');
        return $this->query()->orderBy('time', 'ASC')->where('date', '=', $today)->get();
    }

    public function weekEvents()
    {
        $today = Carbon::now()->format('Y-m-d');
        $upcoming = Carbon::parse($today)->modify("next Sunday");
        return $this->query()
            ->where('date', '>', $today)
            ->where('date', '<=', $upcoming)
            ->orderBy('date', 'ASC')
            ->get();
    }

    public function monthEvents()
    {
        $today = Carbon::now()->format('Y-m-d');
        $upcoming = Carbon::parse($today)->endOfMonth();
        return $this->query()
            ->where('date', '>', $today)
            ->where('date', '<=', $upcoming)
            ->orderBy('date', 'ASC')
            ->get();
    }

    public function yearEvents()
    {
        $today = Carbon::now()->format('Y-m-d');
        $upcoming = Carbon::parse($today)->endOfYear();
        return $this->query()
            ->where('date', '>', $today)
            ->where('date', '<=', $upcoming)
            ->orderBy('date', 'ASC')
            ->get();
    }
}
