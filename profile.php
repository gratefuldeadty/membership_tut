<?php

include 'header.php';

/** This page selects which profile to view using $_GET['id]; */
 
$userid = htmlentities((int)$_GET['id']);

//check to see if profile we are viewing exists.
if ($users->userData($userid)) == false)
{
    Session::set('Error', 'This user does not exist.');
    $users->displayMessage();
    exit;
}

//the profile does exist.
$userData = $users->userData($userid);

echo 'Displaying the profile of '.$username;


