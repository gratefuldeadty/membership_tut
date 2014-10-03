<?php

/** 
 * NOTE: this is procedural style. 
 * $dbh is the database handler. 
 * 
 * make sure you replace 'databasename_here' with whatever database name it is that you are using..
 */

$db_user = 'db_username';
$db_pass = 'db_password';

try 
{
	$dbh = new PDO('mysql:host=localhost;dbname=databasename_here;charset=utf8', $db_user, $db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES, false );
} 
catch(PDOException $e) 
{
    echo 'There seems to be a connection error. Please contact a system admin asap to get this fixed.';
    file_put_contents('connection.errors.txt', $e->getMessage().PHP_EOL,FILE_APPEND);
    exit;
}

?>
