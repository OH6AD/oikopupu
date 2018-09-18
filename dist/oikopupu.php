<?php
/**
 * Oikopupu
 */

require_once __DIR__ . '/../panana.php';
require_once __DIR__ . '/../iptables.php';

// Take parameters from GET arguments
$format = $_GET['format'] ?? 'json';
$skip_str = $_GET['skip'] ?? '';

$panana = getPananaObject();

$output = [];

foreach ($panana as $device) {
    // If either field is empty, that's OK and we are not interested of them
    if ($device->ipv4 === '' || $device->internet_host === '') continue;

    // Resolve Internet IPv4
    $host_raw = $device->internet_host;
    $inet_ipv4 = gethostbyname($host_raw);
    if ($inet_ipv4 === $host_raw) throw new Exception("Unable to resolve hostname $host_raw");

    // Create the record
    array_push($output, [
        'inet_ipv4' => $inet_ipv4,
        'pupu_ipv4' => ipv4_normalize($device->ipv4),
        'dev'       => trim($device->name),
    ]);
}

switch ($format)
{
case 'iptables':
    // Remove elements which contain IPs in skip list
    $output = drop_skip($output, $skip_str);

    // Output final iptables ruleset
    header('Content-Type: text/plain; charset=utf-8');
    print(iptables_format($output));

    break;
case 'json':
    header('Content-Type: application/json; charset=utf-8');
    print(json_encode($output));

    break;
default:
    throw new Exception("Invalid format requested: $format");
}
