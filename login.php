<?php

include 'classes/session.php';

Session::init();

include 'classes/users.php';

$users = new Users($dbh);

if (isset($_POST['doLogin']))
{
	if ($users->doLogin() == true)
	{
		echo 'Login successful!';
	}
	else
	{
		$error = 'Login failed!';
	}
}
