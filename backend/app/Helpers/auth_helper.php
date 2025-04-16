<?php 

/**
 * Hash the token
 * 
 * @param string $token
 * @return string
 */
function hashTokenAuth($token) {
    // hash the user token submitted
    return hash(config('Security')?->algo, substr($token, 0, 35) . config('Security')?->salt);
}

/**
 * Generate the token
 * 
 * @param int $length
 * @return string
 */
function generateTokenAuth($length = 32) {
    return md5(getRandomString($length, 'abcdef1234567890') . microtime(true) . generateUniqId() . config('Security')?->salt);
}

/**
 * Generates a random integer
 *
 * @param int $min
 * @param null|int $max Defaults to max int value
 * @return int
 */
function getRandomInt($min = 0, $max = 5555555)
{
    if (!isset($max)) {
        $max = PHP_INT_MAX;
    }
    return random_int($min, $max);
}

/**
 * Generate random string.
 *
 * @param int $length string length
 * @param string $alphabet characters allowed in random string
 * @return string  random string with given length
 */
function getRandomString($length = 16, $alphabet = "abcdefghijklmnoprstuvwxyz0123456789")
{
    $chars = $alphabet;
    $str   = '';
    for ($i = 0; $i < $length; $i++) {
        $rand_key = getRandomInt(0, strlen($chars) - 1);
        $str .= substr($chars, $rand_key, 1);
    }
    return str_shuffle($str);
}

/**
 * Returns a 32 characters long uniq ID
 *
 * @return string 32 chars
 */
function generateUniqId()
{
    return bin2hex(random_bytes(16));
}