<?php

// Warnings to errors
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Ensure home directory
$config_dir = $_SERVER['HOME']."/.config/oikopupu/";
if (!is_dir($config_dir)) {
    mkdir($config_dir, 0700, TRUE);
}
