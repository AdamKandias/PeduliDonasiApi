# PeduliDonasi API Backend

API Backend untuk aplikasi PeduliDonasi menggunakan Laravel 10.

## Fitur

- **Kampanye Donasi**: Manajemen kampanye donasi untuk berbagai bencana
- **Sistem Donasi**: Integrasi dengan Midtrans untuk pembayaran
- **Riwayat Donasi**: Tracking donasi per user
- **Webhook Midtrans**: Notifikasi otomatis status pembayaran

## Instalasi

1. Clone repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Konfigurasi database di `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=pedulidonasi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Konfigurasi Midtrans di `.env`:
   ```
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
   MIDTRANS_IS_PRODUCTION=false
   ```

7. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```

8. Seed data contoh:
   ```bash
   php artisan db:seed --class=KampanyeSeeder
   ```

9. Jalankan server:
   ```bash
   php artisan serve
   ```

## API Endpoints

### Kampanye
- `GET /api/kampanye` - Daftar kampanye aktif
- `GET /api/kampanye/{id}` - Detail kampanye

### Donasi
- `POST /api/donasi/create` - Buat transaksi donasi baru
- `GET /api/donasi/riwayat/{userId}` - Riwayat donasi user
- `GET /api/donasi/status/{orderId}` - Status transaksi

### User
- `GET /api/user/saldo/{userId}` - Saldo donasi user

### Midtrans Webhook
- `POST /api/midtrans/notification` - Webhook notifikasi Midtrans

## Struktur Database

### Tabel kampanyes
- id, nama, deskripsi, lokasi
- bencana_id, bencana_nama
- target_dana, dana_terkumpul
- status, tanggal_mulai, tanggal_selesai
- gambar_url

### Tabel donasis
- id, kampanye_id, kampanye_nama
- user_id, user_name
- jumlah, pesan, metode_pembayaran
- status, tanggal, order_id
- transaction_id, payment_type
- transaction_status, fraud_status

## Testing

Jalankan test dengan:
```bash
php artisan test
```

## Deployment

1. Set environment ke production
2. Update konfigurasi Midtrans ke production
3. Deploy ke server (Heroku, DigitalOcean, dll)
4. Set webhook URL di Midtrans Dashboard
