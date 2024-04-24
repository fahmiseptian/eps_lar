<?php

namespace App\Libraries;

use App\Models\Member;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Carbon\Carbon;
use App\Libraries\Encryption;
use App\Models\Shop;

class VerificationService
{
    protected $Encryption;

    /**
     * Kirim kode verifikasi ke email pengguna.
     * 
     * @param string $email Email pengguna
     * @return array Hasil pengiriman kode
     */
    public function sendVerificationCode($email)
    {
        // Temukan pengguna berdasarkan email
        $user = Member::where('email', $email)->firstOrFail();

        // Buat kode verifikasi acak dan simpan ke database
        $code = rand(100000, 999999);
        VerificationCode::create([
            'id_user' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(15), // Kode kedaluwarsa dalam 15 menit
        ]);

        // Kirim email dengan kode verifikasi
        Mail::to($email)->send(new VerificationCodeMail($code));

        return ['message' => 'Kode verifikasi telah dikirim ke email Anda.'];
    }

    /**
     * Verifikasi kode yang diberikan oleh pengguna.
     * 
     * @param string $email Email pengguna
     * @param string $code Kode verifikasi
     * @return bool True jika kode valid, False jika tidak valid
     */
    public function verifyCode($email, $code)
    {
        // Temukan pengguna berdasarkan email
        $user = Member::where('email', $email)->firstOrFail();

        // Temukan kode verifikasi berdasarkan user_id dan kode
        $verificationCode = VerificationCode::where('id_user', $user->id)
                                            ->where('code', $code)
                                            ->where('expires_at', '>', Carbon::now())
                                            ->first();

        // Periksa apakah kode verifikasi valid
        if ($verificationCode) {
            // Hapus kode verifikasi setelah digunakan
            $verificationCode->delete();
            return true;
        }

        return false;
    }

    /**
     * Perbarui PIN baru untuk pengguna.
     * 
     * @param string $email Email pengguna
     * @param string $newPin PIN baru
     * @return array Hasil pembaruan PIN
     */
    public function updateNewPin($id_user, $newPin)
    {
        // Temukan pengguna berdasarkan email
        $shop = Shop::where('id_user', $id_user)->firstOrFail();
        $this->Encryption= new Encryption();

        // Perbarui PIN pengguna
        $pin=$this->Encryption->encrypt($newPin);
        $shop->pin_saldo = $pin;
        $shop->save();

        return ['message' => 'PIN berhasil diperbarui.'];
    }
}
