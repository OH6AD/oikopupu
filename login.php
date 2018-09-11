#!/usr/bin/env php
<?php
/**
 * Oikopupu login tool
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/googlesheets.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

// Get the API client and construct the service object.
$client = getGoogleClient($headless = TRUE);
$service = new Google_Service_Sheets($client);

print("Logged in\n");
