<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kampanye extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'lokasi',
        'bencana_id',
        'bencana_nama',
        'target_dana',
        'dana_terkumpul',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'gambar_url'
    ];

    protected $casts = [
        'target_dana' => 'decimal:2',
        'dana_terkumpul' => 'decimal:2',
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime'
    ];

    public function donasis()
    {
        return $this->hasMany(Donasi::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_dana == 0) return 0;
        return ($this->dana_terkumpul / $this->target_dana) * 100;
    }
}
