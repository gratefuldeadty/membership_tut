<!DOCTYPE html>
<html>
<head>
  <title>Register / Login tutorial</title>
  <link rel="stylesheet" type="text/css" href="css/main.css"> <!-- stylesheet -->
</head>
<body>


<div align="center">
    <!-- banner image here -->
    <img src="banner.png" width="99%" height="0"> <!-- make sure you delete that height="0" -->
	
	<div class="home_nav_bar">
    
        <!-- add some links here -->
        <a href="home.php">Homepage</a>
        |
        <a href="forgotpass.php">Forgot Password</a>
	</div>
	
	<!-- out wrapper -->
	<div class="out_wrapper">
	    <div class="left_page"> <!-- begin left -->
	        <form method="POST">
	            <table cellpadding="2" cellspacing="0">
	            	<tr>
	            		<th colspan="2" align="left">Registration Form</th>
	            	</tr>
	            	<tr>
	            		<td style="align:right;"><label for="username">Username:</label></td>
	            		<td><input required type="text" name="username" id="user_id_adv" pattern="[a-zA-Z0-9]{2,64}"></td>
	            	</tr>
	            	<tr>
	            		<td style="align:right;"><label for="email">Email:</label></td>
	            		<td><input required type="email" name="email"></td>
	            	</tr>
	            	<tr>
	            	
	            		<td style="align:right;"><label for="password">Password:</label></td>
	            		<td><input required type="password" name="password" class="password_adv" pattern=".{4,}" autocomplete="off"></td>
	            	</tr>
	            	<tr>
	            		<td style="align:right;"><label for="password_verify">Confirm:</label></td>
	            		<td><input required type="password" name="password_verify" pattern=".{4,}" autocomplete="off">
	            	</tr>
	            	<tr>
	            		<td style="align:right;"><label for="sec_question">Security Question:</label></td>
	            		<td><select name="sec_question">
	            			<option value="0">[ Select a question ]</option>
	            			<option value="1" >What is your mothers maiden name?</option>
	            			<option value="2" >What is your fathers middle name?</option>
	            			<option value="3" >Who was your childhood hero?</option>
	            			<option value="4" >Who is your favorite sports team?</option>
	            			<option value="5" >What was/is your highschool mascot?</option>
	            			<option value="6" >Who is your favorite teacher?</option>
	            			<option value="7" >What is your pets name?</option>
	            			<option value="8" >Who is your favorite band?</option>
	            			<option value="9" >What is your favoite hobby?</option>
	            			<option value="10" >What city were you born in?</option>
	            			<option value="11" >What is your favorite TV show</option>
	            			<option value="12" >What is your favorite movie?</option>
	            			</select>
	            		</td>
	            	</tr>
	            	<tr>
	            		<td style="align:right;"><label for="sec_answer">Security Answer:</label></td>
	            		<td><input required type="text" name="sec_answer" pattern="{2,64}" autocomplete="off"></td>
	            	</tr>
	            	<tr>
	            		<td style="align:right;">
	            			<input type="hidden" name="ip" value="174.63.8.73">
	            			<input type="hidden" name="sessToken" value="542e247d8f642">
	            			<input type="submit" name="register" value="Register" />
	            		</td>
	            	</tr>
	            	<tr>
	            		</tr>
	            </table>
	        </form>
		    
		    
		    <!-- end registration form -->
		    
        </div> <!-- end left -->
		<div class="right_page"> <!-- begin right -->
		    
	
			<form method="POST">
				<table cellpadding="2" cellspacing="0">
					<tr>
						<th colspan="2" align="left">Login Form</th>
					</tr>
					<tr>
						<td style="width:124px;"><label>Username:</label></td>
						<td><input required type="text" name="form_username" pattern="[a-zA-Z0-9]{2,64}"></td>
					</tr>
					<tr>
						<td style="width:124px;"><label>Password:</label></td>
						<td><input required type="password" name="form_password" pattern=".{4,}" autocomplete="off"></td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="formToken" value="542e2a0766ccc">
							<input type="submit" name="doLogin" value="Login">
						</td>
					</tr>
					<tr>
							</tr>
				</table>
			</form>
	
	    
	    </div> <!-- end right -->
</div>

</body>
