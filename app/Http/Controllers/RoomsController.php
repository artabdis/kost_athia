<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomsController extends Controller
{
    /**
     * Menampilkan daftar semua kamar (Halaman Landing / Daftar Kamar)
     */
    public function index()
    {
        return view('kamar');
    }

    

    /**
     * Menampilkan detail Kamar VIP
     */
    public function showVip()
    {
        // Data statis untuk dummy/testing tampilan
        $kamar = [
            'nama' => 'Kamar VIP',
            'lokasi' => 'Lantai Utama (Akses Eksklusif)',
            'harga' => 1500000,
            'status' => 'Tersedia',
            'deskripsi' => 'Kamar eksklusif tipe VIP dengan ukuran lebih luas, pencahayaan alami yang baik, serta ventilasi optimal. Sangat cocok bagi Anda yang menginginkan privasi dan kenyamanan tingkat tinggi selama beristirahat.',
            'fasilitas' => [
                'Kasur Springbed Queen Size',
                'AC (Air Conditioner)',
                'Kamar Mandi Dalam (Water Heater)',
                'Meja & Kursi Kerja',
                'Wi-Fi Cepat (Dedicated)',
                'Lemari Pakaian Besar'
            ]
        ];

        return view('detail-vip', compact('kamar'));
    }

    /**
     * Menampilkan detail kamar secara dinamis berdasarkan ID
     */
    public function show($id)
    {
        // Jika menggunakan Model/Database nanti:
        // $kamar = Room::findOrFail($id);
        // return view('kamar.detail', compact('kamar'));

        return view('detail-vip', ['id' => $id]);
    }

    /**
     * Memproses booking kamar VIP
     */
    public function bookingVip()
    {
        // Logic proses booking (misal: redirect ke form pemesanan atau WhatsApp)
        return redirect()->back()->with('success', 'Permintaan booking Kamar VIP berhasil dikirim!');
    }
}