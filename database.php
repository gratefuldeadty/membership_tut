<?php

//define database settings.
define('DB_HOST', 'localhost');
define('DB_TYPE', 'mysql');
define('DB_NAME', 'dbname');
define('DB_USER', 'dbuser');
define('DB_PASS', 'dbpass');

try 
{
    $dbh = new PDO('DB_TYPE:host=DB_HOST;dbname=DB_NAME', DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES, false );
} 
catch(PDOException $e) 
{
    echo 'There seems to be a connection error. Please report this to a system admin.';
    file_put_contents('logs/connection_errors.txt', $e->getMessage().PHP_EOL,FILE_APPEND);
}

?>
