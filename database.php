<?php

try {
    $dbh = new PDO('mysql:host=localhost;dbname=tasmith_game', $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES, false );
} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}
