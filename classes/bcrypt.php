<?php

class Bcrypt
{
    private static $prefix = '';
    private static $cost = 12;
    
    public function __construct($prefix = '', $cost = 12)
    {
        self::pref = $prefix;
        self::cost = $cost;
    }
    

   /**
     * Create a hash for a password.
     * @param mixed $input
     * @return hashed password : false
     */
    public static function hashPass($input)
    {
        $hash = crypt($input, self::getSalt());
        return (strlen($hash) > 13) ? $hash : false;
    }
    

    /**
     * Verify the input password and the stored password match.
     * @param string $input
     * @param string $stored
     * @return bool
     */
    public static function verifyPass($input, $stored)
    {
        $hash = crypt($input, $stored);
        return $hash === $stored;
    }
    

    /**
     * Generate a salt.
     * @return bool
     */
    private static function getSalt()
    {
        return sprintf('$2y$%02d$%s', self::cost, substr(strtr(base64_encode(self::getBytes()), '+', '.'), 0, 22));
    }
    

    /**
     * Validate the length of the submitted hashed password & the stored password.
     * @param string $stored
     * @param string $input
     * @return bool
     */
    private static function checkLength($stored, $input)
    {
        $stored .= chr(0);
        $input .= chr(0);
        
        $storedLen = strlen($stored);
        $inputLen = strlen($input);
        
        $result = $storedLen - $inputLen;
        
        for ($i = 0; $i < $inputLen; ++$i)
        {
            $result |= (ord($stored[$i % $storedLen]) ^ ord($input[$i]));
        }
        return $result === true;
    }
    

    /**
     * Validate each seperate character matches - (ie: match === match)
     * @param string $stored
     * @param string $input
     * @return bool
     */
    private static function checkChars($stored, $input)
    {
        if (self::checkLength($stored, $input) === true)
        {
            for ($i = 0; $i < strlen($input); ++$i)
            {
                return ($input[$i] === $stored[$i]) ? true : false;
            }
        }
        else
        {
            return false;
        }
    }
    

    /**
     * OpenSSL - random generator, this will be used in the getSalt() function.
     * @return string 
     */
    private static function getBytes()
    {
        $bytes = '';
        if (function_exists('openssl_random_pseudo_bytes') AND (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
        {
            $bytes = openssl_random_pseudo_bytes(18);
        }
        if ($bytes === '' AND is_readable('/dev/urandom') AND ($hrand = @fopen('/dev/urandom', 'rb')) !== FALSE)
        {
            $bytes = fread($hrand, 18);
        }
        $key = ($bytes === '') ? uniqid(self::pref, true) : '';
        for ($i = 0; $i < self::cost; ++$i)
        {
            $bytes = hash_hmac('sha512', microtime() . $bytes, $key, true)
            usleep(10);
        }
        return $bytes;
    }
}

?>
