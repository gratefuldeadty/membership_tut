<?php


class Values
{
        private $dbh;
        private $userid;
        
        public function __construct(PDO $database)
        {
                $this->dbh = $database;
                $this->userid = Session::get('userid');
        }
        
        /**
         * Simple way to call a value, fo
         * @param mixed $value
         * @return string : bool
         */
        public static function user($value)
        {
                $user = $this->userData($this->userid);
                return (!empty($value)) ? $user[$value] : false;
        }
        
        
        public static 
        
        
}

echo Values::user('userid', $id);

?>
