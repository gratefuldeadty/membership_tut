<?php

include 'classes/session.php';
Session::init();
include 'database.php';
include 'classes/users.php';

$users = new Users($dbh);

//if the user is not logged in, redirect them to the index.
if ($users->isLoggedIn() == false)
{
    header('Location: index.php');
}

//user is logged in.
$userData = $users->userData(Session::get('userid'));
$username = $userData['username'];
$userid = $userData['id'];

echo 'Welcome back '.$username.' click <a href="profile.php?id='.$userid.'">here</a> to view your profile.';

echo '<br /><a href="logout.php">Logout</a>';

