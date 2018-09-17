<?php

require_once __DIR__ . '/../panana.php';

$panana = getPananaObject();

header('Content-Type: text/plain; charset=utf-8');
foreach ($panana as $device) {
    if ($device->ipv4 === '' || $device->name === '') continue;
    
    printf("%s %s.pupu\n", ipv4_normalize($device->ipv4), string_safe($device->name));
}
