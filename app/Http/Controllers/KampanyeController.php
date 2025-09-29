<?php

namespace App\Http\Controllers;

use App\Models\Kampanye;
use Illuminate\Http\Request;

class KampanyeController extends Controller
{
    public function index()
    {
        try {
            $kampanyes = Kampanye::where('status', 'aktif')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kampanyes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kampanye',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kampanye = Kampanye::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $kampanye
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kampanye tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
