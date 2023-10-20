<?php

namespace k1lib\utils;

// recibe 69 y retorna 1Z
function decimal_to_n36($number_to_convert) {
    $num_numbers = strlen((string) $number_to_convert);
    $number_to_convert = (float) $number_to_convert;
    $hexChars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base = strlen($hexChars);
    $dig = array();
    $i = 0;
    do {
        $dig[$i] = $number_to_convert % $base;
        $number_to_convert = ($number_to_convert - $dig[$i]) / $base;
        $i++;
    } while ($number_to_convert != 0);

    $result = '';
    foreach ($dig as $value) {
        $result .= substr($hexChars, $value, 1);
    }
    return strrev($result);
}

// recibe 1Z y retorna 69
function n36_to_decimal($number_to_convert) {

    $number_to_convert = strtoupper($number_to_convert);

    $dig = array(
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'A' => 10,
        'B' => 11,
        'C' => 12,
        'D' => 13,
        'E' => 14,
        'F' => 15,
        'G' => 16,
        'H' => 17,
        'I' => 18,
        'J' => 19,
        'K' => 20,
        'L' => 21,
        'M' => 22,
        'N' => 23,
        'O' => 24,
        'P' => 25,
        'Q' => 26,
        'R' => 27,
        'S' => 28,
        'T' => 29,
        'U' => 30,
        'V' => 31,
        'W' => 32,
        'X' => 33,
        'Y' => 34,
        'Z' => 35,
    );

    $decimal_number = 0;
    for ($i = 0; $i <= (strlen($number_to_convert) - 1); $i++) {
        $digit_to_convert = substr($number_to_convert, $i, 1);
        $digit_value = $dig[$digit_to_convert];
        $decimal_number = $decimal_number + (($digit_value * (pow(36, (strlen($number_to_convert) - 1) - $i))));
//        \d('$digit_to_convert : (($digit_value * (pow(35, (strlen($number_to_convert) - 1)-$i)))) = ' . (($digit_value * (pow(36, (strlen($number_to_convert) - 1)-$i)))));
    }

    return $decimal_number;
}

// recibe 1Z y retorna 69
function n62_to_decimal($number_to_convert) {

    $number_to_convert = $number_to_convert;

    $dig = array(
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'a' => 10,
        'b' => 11,
        'c' => 12,
        'd' => 13,
        'e' => 14,
        'f' => 15,
        'g' => 16,
        'h' => 17,
        'i' => 18,
        'j' => 19,
        'k' => 20,
        'l' => 21,
        'm' => 22,
        'n' => 23,
        'o' => 24,
        'p' => 25,
        'q' => 26,
        'r' => 27,
        's' => 28,
        't' => 29,
        'u' => 30,
        'v' => 31,
        'w' => 32,
        'x' => 33,
        'y' => 34,
        'z' => 35,
        'A' => 36,
        'B' => 37,
        'C' => 38,
        'D' => 39,
        'E' => 40,
        'F' => 41,
        'G' => 42,
        'H' => 43,
        'I' => 44,
        'J' => 45,
        'K' => 46,
        'L' => 47,
        'M' => 48,
        'N' => 49,
        'O' => 50,
        'P' => 51,
        'Q' => 52,
        'R' => 53,
        'S' => 54,
        'T' => 55,
        'U' => 56,
        'V' => 57,
        'W' => 58,
        'X' => 59,
        'Y' => 60,
        'Z' => 61,
    );

    $decimal_number = 0;
    for ($i = 0; $i <= (strlen($number_to_convert) - 1); $i++) {
        $digit_to_convert = substr($number_to_convert, $i, 1);
        $digit_value = $dig[$digit_to_convert];
        $decimal_number = $decimal_number + (($digit_value * (pow(62, (strlen($number_to_convert) - 1) - $i))));
//        \d('$digit_to_convert : (($digit_value * (pow(35, (strlen($number_to_convert) - 1)-$i)))) = ' . (($digit_value * (pow(36, (strlen($number_to_convert) - 1)-$i)))));
    }

    return $decimal_number;
}
