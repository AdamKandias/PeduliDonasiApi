<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use App\Models\Kampanye;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Midtrans\Snap;

class DonasiController extends Controller
{
    public function __construct()
    {
        // Authentication middleware
        $this->middleware('auth:api')->except(['index', 'show']);
    }

    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'kampanye_id' => 'required|exists:kampanyes,id',
                'jumlah' => 'required|numeric|min:10000',
                'pesan' => 'nullable|string|max:500',
                'metode_pembayaran' => 'required|string'
            ]);

            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $kampanye = Kampanye::findOrFail($validated['kampanye_id']);

            // generate order id unik
            $orderId = 'PD' . time() . rand(1000, 9999);

            // simpan donasi di database (status pending)
            $donasi = Donasi::create([
                'kampanye_id' => $validated['kampanye_id'],
                'kampanye_nama' => $kampanye->nama,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'jumlah' => $validated['jumlah'],
                'pesan' => $validated['pesan'] ?? '',
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status' => 'pending',
                'tanggal' => now(),
                'order_id' => $orderId
            ]);

            // data untuk midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $donasi->jumlah,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'enabled_payments' => ['bank_transfer', 'gopay', 'qris', 'shopeepay']
            ];

            // minta Snap Token ke midtrans
            $snapToken = Snap::getSnapToken($params);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'order_id' => $donasi->order_id,
                'snap_token' => $snapToken, // ini dipakai di Android
                'data' => $donasi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating donation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function riwayat()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $donasis = Donasi::where('user_id', $user->id)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $donasis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat donasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function status($orderId)
    {
        try {
            $donasi = Donasi::where('order_id', $orderId)->firstOrFail();

            return response()->json([
                'success' => true,
                'status' => $donasi->status,
                'data' => $donasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function confirmPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|string',
                'payment_proof' => 'nullable|string'
            ]);

            $donasi = Donasi::where('order_id', $validated['order_id'])->first();

            if (!$donasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            // Update status to confirmed (manual verification)
            $donasi->status = 'confirmed';
            $donasi->payment_proof = $validated['payment_proof'] ?? null;
            $donasi->save();

            // Update campaign funds
            $this->updateKampanyeSaldo($donasi);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'data' => $donasi
            ]);
        } catch (\Exception $e) {
            Log::error('Payment confirmation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengonfirmasi pembayaran'
            ], 500);
        }
    }

    private function generatePaymentInstructions($donasi, $metodePembayaran)
    {
        switch ($metodePembayaran) {
            case 'bank_transfer':
                return [
                    'method' => 'Bank Transfer',
                    'instructions' => [
                        'Transfer ke rekening: 1234567890 (BCA)',
                        'Atas nama: Yayasan Peduli Donasi',
                        'Jumlah: Rp ' . number_format($donasi->jumlah, 0, ',', '.'),
                        'Catatan: ' . $donasi->order_id,
                        'Setelah transfer, upload bukti pembayaran'
                    ]
                ];
            case 'ewallet':
                return [
                    'method' => 'E-Wallet',
                    'instructions' => [
                        'Kirim ke: 08123456789',
                        'Jumlah: Rp ' . number_format($donasi->jumlah, 0, ',', '.'),
                        'Catatan: ' . $donasi->order_id,
                        'Setelah kirim, upload bukti transfer'
                    ]
                ];
            case 'qris':
                return [
                    'method' => 'QRIS',
                    'instructions' => [
                        'Scan QR code di aplikasi',
                        'Jumlah: Rp ' . number_format($donasi->jumlah, 0, ',', '.'),
                        'Catatan: ' . $donasi->order_id,
                        'Screenshot bukti pembayaran'
                    ]
                ];
            default:
                return [
                    'method' => 'Manual',
                    'instructions' => [
                        'Hubungi admin untuk instruksi pembayaran',
                        'Order ID: ' . $donasi->order_id
                    ]
                ];
        }
    }

    private function updateKampanyeSaldo($donasi)
    {
        $kampanye = Kampanye::find($donasi->kampanye_id);
        if ($kampanye) {
            $kampanye->increment('dana_terkumpul', $donasi->jumlah);
        }
    }
}
