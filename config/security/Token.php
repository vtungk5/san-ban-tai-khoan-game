<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Token
{
    /**
     * Create a JWT token
     *
     * @param array $data Data to include in the token
     * @return string Encoded JWT token
     */
    public static function create(array $data = [])
    {
        try {

            $payload = [
                'data' => $data,
                'iat' => time(), // Issued at the current time
                'nbf' => time(), // Token valid from the current time
                'exp' => time() + 3600 // Token expires in 1 hour
            ];

            // Encode the token
            return JWT::encode(
                $payload,
                file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/config/security/key/private.key'),
                'RS256'
            );
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Validate and decode a JWT token
     *
     * @param string $token JWT token to validate
     * @return object Decoded payload
     */
    public static function check($token)
    {
        try {
            return JWT::decode(
                $token,
                new Key(
                    file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/config/security/key/public.pem'),
                    'RS256'
                )
            );
        } catch (Exception $e) {
            return false;
        }
    }
}
