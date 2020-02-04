<?php

require_once __DIR__ . '/../common.php';

$host_relevant = [
    'host_state',
    'host_acknowledged',
    'host_is_flapping',
    'host_state_type',
    'host_last_state_change',
];

$service_relevant = [
    "service_state",
    "service_acknowledged",
    "service_last_state_change",
    "service_is_flapping",
    "service_state_type",
];

$downtime_relevant = [
    'comment',
    'scheduled_start',
    'scheduled_end',
    'entry_time',
    'is_in_effect',
];

// https://icinga.com/docs/icinga2/latest/doc/03-monitoring-basics/

$enums = [
    "host_state"           => [ 0 => 'UP', 1 => 'DOWN'],
    "service_state"        => [ 0 => 'OK', 1 => 'WARNING', 2 => 'CRITICAL', 3 => 'UNKNOWN'],
    "host_acknowledged"    => [ 0 => false, 1 => true ],
    "service_acknowledged" => [ 0 => false, 1 => true ],
    "host_is_flapping"     => [ 0 => false, 1 => true ],
    "service_is_flapping"  => [ 0 => false, 1 => true ],
    "host_state_type"      => [ 0 => 'SOFT', 1 => 'HARD' ],
    "service_state_type"   => [ 0 => 'SOFT', 1 => 'HARD' ],
];

function enumify($k, $v) {
    global $enums;
    if (array_key_exists($k, $enums)) {
        if (array_key_exists($v, $enums[$k])) {
            return $enums[$k][$v];
        }
    }
    // Fallback to normal value if there's no lookup table or no
    // element matches the lookup table
    return $v;
}         

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_NETRC => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_FAILONERROR => TRUE,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json"
    ],
]);

// Hosts
curl_setopt($ch, CURLOPT_URL, 'https://net.pupu.li/icingaweb2/monitoring/list/hosts?format=json');
$json = curl_exec($ch);
$hosts = json_decode($json, TRUE);

// Services
curl_setopt($ch, CURLOPT_URL, 'https://net.pupu.li/icingaweb2/monitoring/list/services?format=json');
$json = curl_exec($ch);
$services = json_decode($json, TRUE);

// Downtimes
curl_setopt($ch, CURLOPT_URL, 'https://net.pupu.li/icingaweb2/monitoring/list/downtimes?format=json');
$json = curl_exec($ch);
$downtimes = json_decode($json, TRUE);

// Building assoc array for hosts
$root = [];
foreach ($hosts as &$host) {
    unset($out);
    $out = [];
    $root[$host['host_name']] = &$out;

    // Fill in only relevant information
    foreach ($host_relevant as $key) {
        $out[preg_replace('/^host_/', '', $key)] = enumify($key, $host[$key]);
    }

    // Prepare service object
    $out['services'] = [];
}

// Filling in services for each host
foreach ($services as &$service) {
    unset($out);
    $out = [];
    $root[$service['host_name']]['services'][$service['service_display_name']] = &$out;

    // Fill in only relevant information
    foreach ($service_relevant as $key) {
        $out[preg_replace('/^service_/', '', $key)] = enumify($key, $service[$key]);
    }
}

// Fill in downtime data
foreach ($downtimes as &$downtime) {
    unset($out);
    unset($target);

    switch ($downtime['objecttype']) {
    case 'service':
        $target = &$root[$downtime['host_name']]['services'][$downtime['service_display_name']];
        break;
    case 'host':
        $target = &$root[$downtime['host_name']];
        break;
    default:
        // Unknown object type, skip it
        continue;
    }

    // If we already have downtime, check if it is older
    if (array_key_exists('downtime', $target)) {
        if ($target['downtime']['scheduled_start'] < $downtime['scheduled_start']) {
            // We already have more relevant downtime object, we don't
            // want more than one downtime.
            continue;
        }
    }

    $out = [];
    $target['downtime'] = &$out;
    // Fill in only relevant information
    foreach ($downtime_relevant as $key) {
        $out[$key] = $downtime[$key];
    }
}

// Output it
header('Content-Type: application/json');
print(json_encode($root));
