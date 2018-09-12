#!/usr/bin/env php
<?php
/**
 * Oikopupu login tool
 */

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/googlesheets.php';

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

if (count($argv) !== 2) {
    throw new Exception("Usage: ${argv[0]} credentials.json");
}

// Move credentials to config dir
copy($argv[1], $config_dir.'credentials.json');

// Get the API client and construct the service object.
$client = getGoogleClient($headless = FALSE);
$service = new Google_Service_Sheets($client);

print("Logged in\n");
