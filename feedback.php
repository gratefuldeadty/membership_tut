<?php

//display error messages.
if (isset(Session::get('Error')))
{
    foreach (Session::get('Error') as $error)
    {
        echo '<div class="feedback_error">'.$error.'</div>';
    }
}

//display messages 
if (isset(Session::get('Message')))
{
    foreach (Session::get('Message') as $message)
    {
        echo '<div class="feedback_message">'.$message.'</div>';
    }
}
