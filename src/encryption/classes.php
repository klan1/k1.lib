<?php

namespace k1lib;

class crypt {

    /**
     *
     * @var string 64 character key, set as your own always !!
     */
    static protected $key = "bdb07f99c3de1895cdc8795b5091cf9b9aad67692564d88b87f50c91eba233da";

    static function encrypt($value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        $key = pack('H*', self::$key);

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $value, MCRYPT_MODE_CBC, $iv);

        $ciphertext = $iv . $ciphertext;

        $ciphertext_base64 = base64_encode($ciphertext);
        return $ciphertext_base64;
    }

    static function decrypt($value) {

        $key = pack('H*', self::$key);

        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        $ciphertext_dec = base64_decode($value);

        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        $ciphertext_dec = substr($ciphertext_dec, $iv_size);

        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        $plaintext_dec = trim($plaintext_dec);
        if (($json_test = json_decode($plaintext_dec, TRUE)) !== NULL) {
            $plaintext_dec = $json_test;
        }

        return $plaintext_dec;
    }

}
