<?php

/**
* Handles user stuff, ie: login, register, logout, islogged in, session starts, hashing algo, ect. 
* @currently: everything is running properly.
*/

class Users
{
    private $dbh;
    private $ipVerify = false;
    private $emailVerify = false;
                        
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
            Session::setArr('Error', 'Error: The register token is invalid');
            return false;
        }
        elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
        {
            Session::set('Error', 'Username contains invalid characters. Form manipulation is frowned upon.');
            return false;
        }
        elseif (!isset($username) OR !isset($password) OR !isset($vpassword) OR !isset($email) OR !isset($sec_question) OR !isset($sec_answer))
        {
            Session::set('Error', 'You must fill out all fields of the form.');
            return false;
        }
        elseif (!isset($ip))
        {
            Session::set('Error', 'Your IP-address must be visible.')
            return false;
        }
        elseif (strlen($username) < 3 OR strlen($username) > 50)
        {
            Session::set('Error', 'Username must be between 3 and 50 characters in length.');
            return false;
        }
        elseif ($password != $vpassword)
        {
            Session::set('Error', 'The passwords you entered did not verify.');
            return false;
        }
        elseif (strlen($password) < 4)
        {
            Session::set('Error', 'Your password may not be less than 4 characters.');
            return false;
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            Session::set('Error', 'The email address you entered is invalid.');
            return false;
        }
        elseif ($this->checkUsername($username) != false)
        {
            Session::set('Error', 'The username you entered already exists.');
            return false;
        }
        elseif ($this->ipVerify == true AND $_SERVER['REMOTE_ADDR'] != $ip)     
        {
            Session::set('Error', 'Your IP-address has changed during the request.');
            return false;
        }
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

            $password = self::hashPassword($password); //hashing the password.

            $verified = ($emailVerify == true) ? 'false' : 'true';

            $activation_code = ($emailVerify == true) ? sha1(uniqid(mt_rand(), true)) : '';

            //insert the new user
            $query = $this->dbh->prepare('INSERT INTO `stats`
                    (`username`,`email`,`password`,`ip`,`sec_question`,`sec_answer`,`verified`,`activation_code`) 
                VALUES 
                    (?,?,?,?,?,?,?,?)');
            $query->execute(array(
                    $username, $email, $password, $ip, $sec_question, $sec_answer, $verified, $activation_code));
    
            //Email activation set to true:
            if ($emailVerify == true)
            {
                $userid = $this->dbh->lastInsertId();
                if ($this->sendVerificationEmail($userid, $email, $activation_code)) //send the email.
                {
                    Session::sunset('registerToken');
                    Session::set('Message', 'You successfully created an account. Verify your email before logging in.');
                    return true;
                }
                else
                {
                    //delete the user; the email verification process has failed,
                    //we need to let the user know to re-sign up.
                    $query = $this->dbh->prepare('DELETE FROM `stats`
                        WHERE `id` = ?');
                    $query->execute(array($userid));
                    Session::set('Error', 'Activation email has failed. Please try signing up again.')
                    return false;
                }
            }
            else
            {
                //Email verification not required, unset the register token, and return true.
                Session::sunset('registerToken');
                Session::set('Message', 'You have successfully created an account. You may login now.');
                return true;
            }
        }
        else
        {
            Session::set('Error', 'An unknown error has occurred. Please try again.');
            return false;
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
            Session::set('Error', 'The login token is invalid.');
            return false;
        }
        elseif (!isset($username) OR !isset($password))
        {
            Session::set('Error', 'You may not leave any fields blank.')
            return false;
        }
        elseif ($ipVerify == true AND $ip != $_SERVER['REMOTE_ADDR'])
        {
            Session::set('Error', 'Your ip has ')
            return false;
        }
        elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
        {
            Session::set('Error', 'Username contains invalid characters. Form manipulation is frowned upon.');
            return false;
        }
        elseif ($this->checkUsername($username) == false)
        {
            Session::set('Error', 'Username does not exist.');
            return false;
        }
        elseif ($this->ipConfig == true AND $ip != $_SERVER['REMOTE_ADDR'])
        {
            Session::set('Error', 'Your IP-address has changed during the request.');
            return false; //again, if ipConfig == true, it will need to validate that ip's match.
        }

        //define user const
        $userData = $this->checkUsername($username);
        $userid = $userData['id'];
        $user_pass = $userData['password'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $verified = $userData['verified'];
        if (self::checkPassword($user_pass, $password))
        {
            if ($verified == 'false')
            {
                Session::set('Error', 'You must verify your email before logging in.');
                return false;
            }
            
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
            
            return true; //login successful.
        }
        else
        {
            Session::get('Error', 'The password you entered was incorrect.');
            return false;
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
     * Displaying feedback messages, (errors/messages)
     * and set them to null.
     */
    public function displayFeedback()
    {
        // set the error/message blank.
        Session::set('Error', null);
        Session::set('Message', null);
    }
    
    /**
     * System for regenerating session ids.
     * When the 'count' hits 10, regenerate (false).
     * When the 'count' hits 5 and 'flagged' is set,
     * regenerate (true)
     */
    public static function checkSessCount()
    {
    	$count = Session::get('count');
    	if (($count -= 1) == 0)
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
}
