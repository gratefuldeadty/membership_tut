<?php

/**
* Sessions
*/

class Session
{
    /**
     * Starts the session
     */
    public static function init()
    {
        if (session_id() == '') 
        {
            session_start();
        }
    }

    /**
     * sets a specific value to a specific key of the session
     * @param mixed $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    
    public static function sunset($key)
    {
    	unset($_SESSION[$key]);
    }

    /**
     * gets/returns the value of a specific key of the session
     * @param mixed $key Usually a string, right ?
     * @return mixed
     */
    public static function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    
    public static function reg($value)
    {
    	session_regenerate_id($value);
    }

    /**
     * deletes the session (= logs the user out)
     */
    public static function destroy()
    {
        session_destroy();
    }
}
