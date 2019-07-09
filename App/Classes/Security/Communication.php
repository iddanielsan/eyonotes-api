<?php

namespace App\Classes\Security;
use phpseclib\Crypt\RSA;

class Communication{
    public static function decode($data){
        $rsa = new RSA();
        $rsa->loadKey(file_get_contents(BASE_PATH.'App/extra/rsa/server/private.key'));
        $rsa->setEncryptionMode(RSA::ENCRYPTION_OAEP);
        
        return $rsa->decrypt(base64_decode($data));
    }
    
    public static function encode(){
        
    }
}