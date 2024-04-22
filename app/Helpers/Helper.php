<?php

function code($digits = 15){

    $random_hash = bin2hex(random_bytes($digits));
    return substr($random_hash, 0, 8);
}

function uniqueNumber(int $length){
    $prefix = "WAPI-";
    $rand = rand(15,20000).time();
    $res = substr($rand, 2, 6);
    return $prefix."{$res}";
}

/**
 * Calculate and return monitary value
 */
function money($value)
{
    return number_format((float)$value, 2, '.', '');
}

/** Returns a random alphanumeric token or number
 * @param int length
 * @param bool  type
 */
function getRandomToken($length, $typeInt = false)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet);

    if ($typeInt == true) {
        for ($i = 0; $i < $length; $i++) {
            $token .= rand(0, 9);
        }
        $token = intval($token);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
    }

    return $token;
}

function report_error(\Exception $e){

    logger("Error Occurred:: " . json_encode([
            "line"      => $e->getLine(),
            "file"      => $e->getFile(),
            "message"   => $e->getMessage(),
            "trace"     => $e->getPrevious()
        ]));

    return null;
}
