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
        
       if (!self::checkLength($hash, $stored))
        {
            return false;
        }
        
        //cheap way to check the lengths
        if (strlen($input) !== strlen($stored))
        {
            return false;
        }
        
        //check to make sure each character matches (ie: match == match)
        for ($i = 0; $i < strlen($input); ++$i)
        {
            if ($input[$i] !== $stored[$i])
            {
                return false;
            }
            usleep(10);
        }
        return $hash === $stored;
    }
    

    /**
     * Generate a salt.
     * @return bool
     */
    private static function getSalt()
    {
    	return sprintf('$2y$%02d$%s', self::$cost, substr(strtr(base64_encode(self::getBytes()), '+', '.'), 0, 22));
    }
    

    /**
     * OpenSSL - random generator, this will be used in the getSalt() function.
     * @return string 
     */
    private static function getBytes() 
    {	
	    $bytes = '';
	    if(function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) 
	    {
	        $bytes = openssl_random_pseudo_bytes(18);
	    }
	 
	    if($bytes === '' && is_readable('/dev/urandom') && ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE) 
	    {
	        $bytes = fread($hRand, 18);
	        fclose($hRand);
	    }
	    $key = ($bytes === '') ? uniqid(self::$pref, true);
	    for($i = 0; $i < 12; $i++) 
	    {
	        $bytes = hash_hmac('sha512', microtime() . $bytes, $key, true);
	        usleep(10);
	    }
        return $bytes;
    }
}

?>
