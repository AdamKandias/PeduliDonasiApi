<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kampanye_id',
        'kampanye_nama',
        'user_id',
        'user_name',
        'jumlah',
        'pesan',
        'metode_pembayaran',
        'status',
        'tanggal',
        'order_id',
        'transaction_id',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'payment_proof'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'datetime'
    ];

    public function kampanye()
    {
        return $this->belongsTo(Kampanye::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
