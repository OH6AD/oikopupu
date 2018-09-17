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

    return $values;
}

function ipv4_normalize($ipv4_raw) {
    // Validate IPv4
    preg_match('/\s*^0*([0-9]+)\.0*([0-9]+)\.0*([0-9]+)\.0*([0-9]+)\s*$/', $ipv4_raw, $matches);
    if (array_shift($matches) === NULL) throw new Exception("Invalid IP address: $ipv4_raw");
    return implode('.', $matches);
}

function string_safe($raw, $safechar='-') {
    $safechar_esc = preg_quote($safechar);
    $ascii = strtolower(iconv("utf-8","ascii//TRANSLIT", $raw));
    $alphanum = preg_replace(['/[^a-z0-9]/', "/$safechar_esc+/"], $safechar, $ascii);
    return trim($alphanum, $safechar);
}

function getPananaObject() {
    $values = getPanana();
    $header = array_shift($values);
    $header_map = array_flip($header);

    // Take only specified headers
    $take = [
        'IPv4'            => 'ipv4',
        'Internet-reitti' => 'internet_host',
        'Laite'           => 'name',
    ];

    foreach($values as &$row) {
        $assoc_in = array_combine($header, array_pad($row, count($header), ''));
        $assoc_out = [];

        foreach($take as $old => $good) {
            if (!array_key_exists($old, $assoc_in)) {
                throw new Exception("Unable to find PANANA header '$old'");
            }
            $assoc_out[$good] = trim($assoc_in[$old]);
        }
        $row = (object)$assoc_out;
    }

    return $values;
}
