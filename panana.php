<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/googlesheets.php';

function getPanana() {
	// Get the API client and construct the service object.
    $client = getGoogleClient();
    $service = new Google_Service_Sheets($client);

	// Read IP allocations from Pupu Assigned Names And Numbers Authority (PANANA)
    $spreadsheetId = '1_lFzWQ_vjAgZVzJ1Alpo0noRWXfNiwiDLibN31orwmU';
    $range = 'Hosts!A1:M';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (empty($values)) {
        throw new Exception("No data found");
    }

    $header = array_shift($values);
    return [array_flip($header), $values];
}
