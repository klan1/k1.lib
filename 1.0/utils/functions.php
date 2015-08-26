<?php
namespace k1lib\utils;

// recibe 69 y retorna 1Z
function decimal_to_n36($number_to_convert) {
    $num_numbers = strlen((string) $number_to_convert);
    $number_to_convert = (float) $number_to_convert;
    $hexChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $base = strlen($hexChars);
    $dig = array();
    $i = 0;
    do {
        $dig[$i] = $number_to_convert % $base;
        $number_to_convert = ($number_to_convert - $dig[$i]) / $base;
        $i++;
    } while ($number_to_convert != 0);

    $result = "";
    foreach ($dig as $value) {
        $result.= substr($hexChars, $value, 1);
    }
    return strrev($result);
}

// recibe 1Z y retorna 69
function n36_to_decimal($number_to_convert) {

    $number_to_convert = strtoupper($number_to_convert);

    $dig = array(
        "0" => 0,
        "1" => 1,
        "2" => 2,
        "3" => 3,
        "4" => 4,
        "5" => 5,
        "6" => 6,
        "7" => 7,
        "8" => 8,
        "9" => 9,
        "A" => 10,
        "B" => 11,
        "C" => 12,
        "D" => 13,
        "E" => 14,
        "F" => 15,
        "G" => 16,
        "H" => 17,
        "I" => 18,
        "J" => 19,
        "K" => 20,
        "L" => 21,
        "M" => 22,
        "N" => 23,
        "O" => 24,
        "P" => 25,
        "Q" => 26,
        "R" => 27,
        "S" => 28,
        "T" => 29,
        "U" => 30,
        "V" => 31,
        "W" => 32,
        "X" => 33,
        "Y" => 34,
        "Z" => 35,
    );

    $decimal_number = 0;
    for ($i = strlen($number_to_convert) - 1; $i >= 0; $i--) {
        $digit_to_convert = substr($number_to_convert, $i, 1);
        $digit_value = $dig[$digit_to_convert];
        $decimal_number = $decimal_number + (($digit_value * (pow(35, $i))));
    }

    return $decimal_number;
}
