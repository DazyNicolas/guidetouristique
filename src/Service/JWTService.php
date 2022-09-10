<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    //On génère le token

    /**
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param array $validity
     * @param int $validity
     * @return string
     */

    public function generate (array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if($validity > 0){
            $now = new DateTimeImmutable();
            $expiration = $now->getTimestamp() + $validity;
    
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $expiration;
        }

       

        //on encode en base64
        $base64Header= base64_encode(json_encode($header));
        $base64Payload= base64_encode(json_encode($payload));

        //on "nettoie" les valeurs encodées (retrait des+,/ et =)

        $base64Header = str_replace(['+', '/', '='],['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='],['-', '_', ''], $base64Payload);


        

        // ON génere le signature

        $secret = base64_encode($secret);
        $singnature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);
        $base64Signature = base64_encode($singnature);
        $base64Signature = str_replace(['+', '/','='],['-', '_',''], $base64Signature);
       

        // On crée le token

        $jwt = $base64Header. '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    //On vérifie que le token est valide (correctemen formé)

    public function isValid(string $token) : bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        )===1;
    }

    // On régupère le payload

    public function getPayload(string $token) :array
    {
        //On demonte le token
        $array = explode('.', $token);

        //On decode le Payload
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;

    }
    // On régupère le header

    public function getHeader(string $token) :array
    {
        //On demonte le token
        $array = explode('.', $token);

        //On decode le Payload
        $header = json_decode(base64_decode($array[0]), true);

        return $header;

    }

     // On vérifie si le token a expiré
     public function isExpired($token) :bool
     {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();
    
         return $payload['exp'] < $now->getTimestamp();
     }

     //On vérifie la signature du token

     public function check(string $token, string $secret)
     {
        //On récupère le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        //On régénère un token
        $veriftoken = $this->generate($header, $payload, $secret, 0);

        return $token === $veriftoken;
     }


}