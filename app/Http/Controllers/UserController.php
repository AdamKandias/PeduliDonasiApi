<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Donasi;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function saldo(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            // Calculate total donation amount
            $totalDonasi = Donasi::where('user_id', $user->id)
                ->whereIn('status', ['success', 'confirmed'])
                ->sum('jumlah');

            return response()->json([
                'success' => true,
                'saldo' => $totalDonasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil saldo user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
