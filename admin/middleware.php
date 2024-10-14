<?php
require_once '../vendor/autoload.php'; // Composer autoload fájl
use \Firebase\JWT\JWT;

class Middleware {
    private static $secretKey = 'titkos_kulcs'; // Titkos kulcs a JWT számára

    public static function validateToken() {
        // Ellenőrizzük, hogy van-e token a fejlécben
        $headers = apache_request_headers();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Token nem található.']);
            exit();
        }

        // Kivesszük a "Bearer " részt a tokenből
        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            // Token dekódolása
            $decoded = JWT::decode($token, self::$secretKey, array('HS256'));
            // Token validálása sikeres
            return $decoded;
        } catch (Exception $e) {
            // Hibás vagy lejárt token
            http_response_code(401);
            echo json_encode(['error' => 'Hibás vagy lejárt token.']);
            exit();
        }
    }
}
