<?php

namespace k1lib;

class crypt {

    /**
     *
     * @var string 64 character key, set as your own always !!
     */
    static protected $key = "bdb07f99c3de1895cdc8795b5091cf9b9aad67692564d88b87f50c91eba233da";
    static private $cipher = "aes-128-gcm";
    static private $iv_send_lenght = 24;
    static private $tag_send_lenght = 32;

    static function encrypt($value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $ivlen = openssl_cipher_iv_length(static::$cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $iv_64 = bin2hex($iv);
        $ciphertext = openssl_encrypt($value, static::$cipher, static::$key, $options = 0, $iv, $tag);
        $tag_64 = bin2hex($tag);

        $return_value = ($iv_64 . $tag_64 . $ciphertext);
        return $return_value;
    }

    static function decrypt($value) {
        $value = ($value);
        $iv_64 = substr($value, 0, static::$iv_send_lenght);
        $tag_64 = substr($value, static::$iv_send_lenght, static::$tag_send_lenght);
        $iv = hex2bin($iv_64);
        $tag = hex2bin($tag_64);
        $ciphertext = substr($value, static::$iv_send_lenght + static::$tag_send_lenght);
        $original_plaintext = openssl_decrypt($ciphertext, static::$cipher, static::$key, $options = 0, $iv, $tag);

        if (($json_test = json_decode($original_plaintext, TRUE)) !== NULL) {
            $original_plaintext = $json_test;
        }
        return $original_plaintext;
    }

}
