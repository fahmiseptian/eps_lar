<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Support\Facades\Config;

class JWTService
{
    public static function validateToken($token)
    {
        $key = Config::get('jwt.key');
        
        try {
            // Decode the token using the key
            return JWT::decode($token, $key, ['HS256']); // Pastikan algoritma sesuai

        } catch (ExpiredException $e) {
            // Tangani jika token sudah kedaluwarsa
            return null; // atau throw exception
        } catch (SignatureInvalidException $e) {
            // Tangani jika tanda tangan tidak valid
            return null; // atau throw exception
        } catch (\Exception $e) {
            // Tangani pengecualian lainnya
            return null; // atau throw exception
        }
    }

    public static function generateToken($data)
    {
        $key = Config::get('jwt.key');
        return JWT::encode($data, $key, 'HS256'); // Menggunakan algoritma HS256
    }
}
