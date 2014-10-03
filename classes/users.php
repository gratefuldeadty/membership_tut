<?php

/**
* Handles user stuff, ie: login, register, logout, islogged in, session starts, hashing algo, ect. 
* @currently: everything is running properly.
*/

class Users
{
    private $dbh;
    private static $algo = '$2a';
    private static $cost = '$10';
    public $ipConfig = true; //default, the IP from hidden form fields needs to match the users current IP.
                             //to avoid the IP check, set $ipConfig to false. Ip still cannot be left empty.
    /**
     * Constructor  
     */
    public function __construct($database)
    {
        $this->dbh = $database;
    }
    
    /**
     * Fetch the the users data
     * @param int $userid
     * @return fetched result : bool (false)
     */
    public function userData($userid)
    {
        $query = $this->dbh->prepare('SELECT * FROM `stats`
            WHERE `id` = ?');
        $query->execute(array($id));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    /**
     * Check to see if the username exists
     * @param string $username
     * @returns fetch result 'username' & 'id' : bool (false)
     */
    public function checkUsername($username)
    {
        $query = $this->dbh->prepare('SELECT `username`,`id` FROM `stats`
            WHERE `username` = ?');
        $query->execute(array($username));
        return ($query->rowCount() > 0) ? $query->fetch() : false;
    }
    
    /**
     * Perform registration proccess.
     * @return bool 
     */
    public function doRegister()
    {
        $sess_token = Session::get('registerToken');
        $form_token = $_POST['registerToken'];
        $username = htmlentities($_POST['username']);
        $password = htmlentities($_POST['password']);
        $vpassword = htmlentities($_POST['password_verify']);
        $email = htmlentities($_POST['email']);
        $ip = htmlentities($_POST['ip']);
        $sec_question = htmlentities($_POST['sec_question']);
        $sec_answer = htmlentities($_POST['sec_answer']);
        if (!self::checkToken($form_token, $sess_token))
        {
            return false;
        }
        elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
        {
            return false;
        }
        elseif (!isset($username) OR !isset($password) OR !isset($vpassword) OR !isset($email) OR !isset($ip) OR !isset($sec_question) OR !isset($sec_answer))
        {
            return false;
        }
        elseif (strlen($username) < 3 OR strlen($username) > 50)
        {
            return false;
        }
        elseif ($password != $vpassword)
        {
            return false;
        }
        elseif (strlen($password) < 4)
        {
            return false;
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return false;
        }
        elseif ($this->checkUsername($username) != false)
        {
            return false;
        }
        elseif ($this->ipConfig == true AND $_SERVER['REMOTE_ADDR'] != $ip)     
        {                                   // the register page has a hidden field for IP.                                       // if it changes, false is returned.
            return false;                   // for AOL users, this will be a problem.
        }                                   // to avoid this, set $ipConfig to false.
        elseif (isset($username) 
            AND isset($password) 
            AND isset($vpassword) 
            AND isset($email) 
            AND isset($ip) 
            AND isset($sec_question) 
            AND isset($sec_answer)
            AND filter_var($email, FILTER_VALIDATE_EMAIL)
            AND preg_match('/^[a-z\d]{2,64}$/i', $username)
            AND strlen($username) > 2 AND strlen($username) < 50
            AND strlen($password) > 3)
        {
            //everything checked.

            $password = self::hashPassword($password); //hashing the password.

            //insert the new user
            $query = $this->dbh->prepare('INSERT INTO `stats`
                (`username`,`email`,`password`,`ip`,`sec_question`,`sec_answer`) VALUES (?,?,?,?,?,?)');
            $query->execute(array($username, $email, $password, $ip, $sec_question, $sec_answer));
    
            Session::sunset('registerToken'); //unset (remove) the register token.
            
            //return bool, registration successful!.
            return true;
        }
    }
    
    /**
     * Perform login proccess.
     * @return bool
     */
    public function doLogin()
    {
        $username = htmlentities($_POST['username']);
        $password = htmlentities($_POST['password']);
        $ip = htmlentities($_POST['ip']);
        $form_token = $_POST['loginToken'];
        $sess_token = $_SESSION['loginToken'];

        $time = time() - 10*60; //now - 10 mins
        
        $query = $this->dbh->prepare('SELECT `ip` FROM `failed_logins`
            WHERE `time` > ?');
        $query->execute(array($time));
        if ($query->rowCount() >= 3)
        {
            return false; //the user must wait 10 minutes to try logging in again.
        }
        elseif (!self::checkToken($form_token, $sess_token))
        {
            return false;
        }
        elseif (!isset($username) OR !isset($password) OR !isset($ip))
        {
            return false;
        }
        elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
        {
            return false;
        }
        elseif ($this->checkUsername($username) == false)
        {
            return false;
        }
        elseif ($this->ipConfig == true AND $ip != $_SERVER['REMOTE_ADDR'])
        {
            return false; //again, if ipConfig == true, it will need to validate that ip's match.
        }

        //define user const
        $userData = $this->checkUsername($username);
        $userid = $userData['id'];
        $user_pass = $userData['password'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (self::checkPassword($user_pass, $password))
        {
            
            //update the users timestamp, well use it to generate an online status.
            $query = $this->dbh->prepare('UPDATE `stats` SET `login_timestamp` = ?
                WHERE `id` = ?');
            $query->execute(array(time(),$userid));
            
            //start and set the session for the logged in user.
            Session::init();
            Session::set('logged_in', true);
            Session::set('userid', $userid);
            Session::set('userAgent', $agent);
            Session::set('count', 5);
            Session::sunset('loginToken'); //unset (remove) the login token.
            
            return true; //login successful, return true (bool) login successful.
        }
        else
        {
            return false; //hashed passwords did not match.
        }
    }
    
    /**
     * Verifies if the user is logged in or not (count must = 4 to return true.)
     * @return bool
     */
    public function isLoggedIn()
    {
        $check = 0;
        if (Session::get('logged_in') == true)
        {
            ++$check;    
        }
        if (Session::get('userid'))
        {
            ++$check;
        }
        if (Session::get('userAgent'))
        {
            ++$check;
        }
        if (Session::get('userAgent') == $_SERVER['HTTP_USER_AGENT'])
        {
            ++$check;
        }
        return ($check == 4) ? true : false;
    }
    
    /**
     * System for regenerating session ids.
     * When the 'count' hits 10, regenerate (false).
     * When the 'count' hits 5 and 'flagged' is set,
     * regenerate (true)
     */
    public static function checkSessCount()
    {
    	if (($_SESSION['count'] -= 1) == 0)
    	{        
            Session::sunset('count'); //unset count.
            Session::set('count', 10); //reset count.
            Session::set('flagged', true); //flag to regenerate_id
       	    session_regenerate_id(false); //regenerate false.
        }
        self::checkSessExpire(); //run check expired function.
    }
    
    /**
     * Regenerates the session id (true)
     */
    public static function checkSessExpire()
    {
        //only when the count is 3 and flagged is set!
        if (Session::get('count') == 5 AND Session::get('flagged') == true)
        {
            Session::sunset('flagged'); //unset flagged session.
            session_regenerate_id(true); //now generate true.
        }
    }
    
    /**
     * Destroys the users session, and logs them out.
     */
    public static function logOut()
    {
        Session::destroy();
    }

    /**
     * Create a random salt
     * @returns mixed
     */
    public static function uniqueSalt()
    {
        return substr(sha1(mt_rand()),0,22);
    }
    
    /**
     * Hash the password
     * @param string $password
     * @return hashed password
     */
    public static function hashPassword($password)
    {
        return crypt($password, self::$algo . self::$cost . '$' . self::uniqueSalt());
    }
    
    /**
     * Verify password
     * @param string $hash
     * @param string $password
     * @return bool
     */
    public static function checkPassword($hash, $password)
    {
        $salt = substr($hash, 0, 29);
        $new_hash = crypt($password, $salt);
        return ($hash == $new_hash);
    }
    
    /**
     * Verify form tokens match the session
     * @param string $token
     * @param string $session
     * @return bool
     */
    public static function checkToken($token, $session)
    {
        return ($token == $session);
    }
}
