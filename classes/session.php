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
     * Sets a value of a session based off the key.
     * @param mixed $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Set a value to a session array.
     * @param mixed $key
     * @param mixed $value
     */
    public static function setArr($key, $value)
    {
        $_SESSION[$key][] = $value;
    }
    
    
    /**
     * unset a session.
     * @param mixed $key
     */
    public static function sunset($key)
    {
    	unset($_SESSION[$key]);
    }

    /**
     * Get the specific session - if existing.
     * @param mixed $key
     * @return mixed : ''
     */
    public static function get($key)
    {
        return (isset($_SESSION[$key])) ? $_SESSION[$key] : '';
    }
    
    /**
     * Delete the current session.
     */
    public static function destroy()
    {
        session_destroy();
    }
}
