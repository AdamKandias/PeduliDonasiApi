<?php

namespace Database\Seeders;

use App\Models\Kampanye;
use Illuminate\Database\Seeder;

class KampanyeSeeder extends Seeder
{
    public function run()
    {
        $kampanyes = [
            [
                'nama' => 'Bantuan Korban Gempa Jakarta',
                'deskripsi' => 'Bantuan untuk korban gempa yang terjadi di Jakarta. Dana akan digunakan untuk kebutuhan darurat, makanan, obat-obatan, dan rekonstruksi rumah.',
                'lokasi' => 'Jakarta',
                'bencana_id' => 'gempa_jakarta_2024',
                'bencana_nama' => 'Gempa Jakarta 2024',
                'target_dana' => 500000000,
                'dana_terkumpul' => 125000000,
                'status' => 'aktif',
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(30),
                'gambar_url' => 'https://example.com/gempa-jakarta.jpg'
            ],
            [
                'nama' => 'Bantuan Korban Banjir Bandung',
                'deskripsi' => 'Bantuan untuk korban banjir yang melanda Bandung. Dana akan digunakan untuk evakuasi, tempat tinggal sementara, dan kebutuhan sehari-hari.',
                'lokasi' => 'Bandung',
                'bencana_id' => 'banjir_bandung_2024',
                'bencana_nama' => 'Banjir Bandung 2024',
                'target_dana' => 300000000,
                'dana_terkumpul' => 75000000,
                'status' => 'aktif',
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(45),
                'gambar_url' => 'https://example.com/banjir-bandung.jpg'
            ],
            [
                'nama' => 'Bantuan Korban Longsor Bogor',
                'deskripsi' => 'Bantuan untuk korban longsor di Bogor. Dana akan digunakan untuk evakuasi, pencarian korban, dan rehabilitasi area terdampak.',
                'lokasi' => 'Bogor',
                'bencana_id' => 'longsor_bogor_2024',
                'bencana_nama' => 'Longsor Bogor 2024',
                'target_dana' => 200000000,
                'dana_terkumpul' => 50000000,
                'status' => 'aktif',
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(60),
                'gambar_url' => 'https://example.com/longsor-bogor.jpg'
            ]
        ];

        foreach ($kampanyes as $kampanye) {
            Kampanye::create($kampanye);
        }
    }
}
