<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnimeModel;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Inisialisasi konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // Method untuk menampilkan halaman pembayaran
    public function showPaymentPage($animeId)
    {
        $anime = AnimeModel::findOrFail($animeId);

        // Membuat order ID unik
        $orderId = 'ANIME-' . $anime->id . '-' . time();
        $grossAmount = 15000;

        // Menyimpan data order ke database
        $order = Order::create([
            'order_id' => $orderId,
            'user_id' => Auth::id(),
            'anime_id' => $anime->id,
            'gross_amount' => $grossAmount,
        ]);

        // Parameter untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => $order->gross_amount,
            ],
            'item_details' => [[
                'id' => $anime->id,
                'price' => $grossAmount,
                'quantity' => 1,
                'name' => 'Pembelian Akses Anime: ' . $anime->judul,
            ]],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return view('anime.payment', compact('snapToken', 'order', 'anime'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
        }
    }

    // Method untuk menangani notifikasi webhook dari Midtrans
    public function notificationHandler(Request $request)
    {
        // Memverifikasi notifikasi
        $notification = new Notification();

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;

        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        // Update status transaksi berdasarkan notifikasi
        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $order->transaction_status = 'challenge';
                } else {
                    $order->transaction_status = 'success';
                }
            }
        } else if ($transaction == 'settlement') {
            $order->transaction_status = 'settlement';
        } else if ($transaction == 'pending') {
            $order->transaction_status = 'pending';
        } else if ($transaction == 'deny') {
            $order->transaction_status = 'denied';
        } else if ($transaction == 'expire') {
            $order->transaction_status = 'expire';
        } else if ($transaction == 'cancel') {
            $order->transaction_status = 'canceled';
        }

        // Menyimpan seluruh response dari Midtrans untuk audit
        $order->midtrans_response = $notification->getResponse();
        $order->payment_type = $type;

        if (isset($notification->va_numbers[0])) {
            $order->va_number = $notification->va_numbers[0]->va_number;
            $order->bank_name = $notification->va_numbers[0]->bank;
        }

        $order->save();

        return response()->json(['message' => 'Notifikasi berhasil diproses']);
    }
}