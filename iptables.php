#!/usr/bin/env php
<?php
/**
 * Oikopupu
 */

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/googlesheets.php';

// Take parameters from GET arguments, if not HTTP then use environment.
if (empty($_GET)) {
    $format = getenv("format");
    $skip_str = getenv("skip");
} else {
    $format = $_GET['format'];
    $skip_str = $_GET['skip'];
}

// Assuming input from `hostname -I` which is list of IP addresses
// delimited by spaces. Creating array from them.
$skip = empty($skip_str) ? [] : explode(" ", $skip_str);

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
$ipv4_i = array_search("IPv4", $header);
$internet_i = array_search("Internet-reitti", $header);
$dev_i = array_search("Laite", $header);

if ($ipv4_i === FALSE) throw new Exception("Unable to find header 'IPv4'");
if ($internet_i === FALSE) throw new Exception("Unable to find header 'Internet-reitti'");

$output = [];

foreach ($values as $row) {
    $pupu_ipv4_raw = trim($row[$ipv4_i] ?? '');
    $host_raw = trim($row[$internet_i] ?? '');
    $dev = trim($row[$dev_i] ?? '');

    // If either field is empty, that's OK and we are not interested of them
    if ($pupu_ipv4_raw === '' || $host_raw === '') continue;

    // Validate Pupu IPv4
    preg_match('/^0*([0-9]+)\.0*([0-9]+)\.0*([0-9]+)\.0*([0-9]+)$/', $pupu_ipv4_raw, $matches);
    if (array_shift($matches) === NULL) throw new Exception("Invalid IP address: $line");
    $pupu_ipv4 = implode('.', $matches);

    // Resolve Internet IPv4
    $inet_ipv4 = gethostbyname($host_raw);
    if ($inet_ipv4 === $host_raw) throw new Exception("Unable to resolve hostname $host_raw");

    // Skip own address if matches to either Pupu or Internet address
    if (array_search($inet_ipv4, $skip, TRUE) !== FALSE) continue;
    if (array_search($pupu_ipv4, $skip, TRUE) !== FALSE) continue;

    // Create the record
    array_push($output, [
        'inet_ipv4' => $inet_ipv4,
        'pupu_ipv4' => $pupu_ipv4,
        'dev' => $dev
    ]);
}

switch ($format)
{
case 'iptables':
    // Get longest comment length
    $comment_len = array_reduce($output, function($carry, $a) {
        return max($carry, strlen($a['dev']));
    }, 0);

    // Print header boilerplate
    print("*nat\n-F PUPU_DNAT\n-F PUPU_SNAT\n");

    // Produce rules
    foreach($output as $a) {
        $comment_arg = '"'.escapeshellcmd(sprintf("%-${comment_len}s", $a['dev'])).'"';
        printf(
            "-A PUPU_DNAT -d %s -j DNAT --to-destination %s -m comment --comment %s\n".
            "-A PUPU_SNAT -d %s -j MASQUERADE -m comment --comment %s\n",
            $a['inet_ipv4'], $a['pupu_ipv4'], $comment_arg, $a['inet_ipv4'], $comment_arg
        );
    }

    // Print footer boilerplate
    print("COMMIT\n");
    break;
case 'json':
    print(json_encode($output));
    break;
default:
    throw new Exception("Invalid format requested");
}
