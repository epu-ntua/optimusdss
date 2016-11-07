<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['username'] = null;
$_SESSION['user'] = null;

header('Location: index.php');


