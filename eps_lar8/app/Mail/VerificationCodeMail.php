<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationCodeMail extends Mailable
{
    use SerializesModels;

    public $code;

    /**
     * Buat instance pesan email.
     *
     * @param string $code Kode verifikasi yang akan dikirim.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Bangun pesan email.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Kode Verifikasi Anda')
            ->view('emails.verification_code')
            ->with([
                'code' => $this->code,
            ]);
    }
}
