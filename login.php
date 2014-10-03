<?php

/**
 * Example usage on how to use the login function.
 */

include 'classes/session.php';

Session::init(); //our session_start.

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
		echo 'Login failed!!'
	}
}

if (!Session::get('loginToken'))
{
	Session::set('loginToken', uniqid()); //if the token is not set, 
}
?>

<div align="center">
	<form method="POST">
		<table cellpadding="2" cellspacing="0">
			<tr>
				<th colspan="2" align="left">Login</th>
			</tr>
			<tr>
				<td style="width:124px;"><label>Username:</label></td>
				<td><input required type="text" name="username" pattern="[a-zA-Z0-9]{2,64}"></td>
			</tr>
			<tr>
				<td style="width:124px;"><label>Password:</label></td>
				<td><input required type="password" name="password" pattern=".{4,}" autocomplete="off"></td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">
					<input type="hidden" name="loginToken" value="<?php echo Session::get('loginToken'); ?>">
					<input type="submit" name="doLogin" value="Login">
				</td>
			</tr>
			<tr>
			</tr>
		</table>
	</form>
</div>
