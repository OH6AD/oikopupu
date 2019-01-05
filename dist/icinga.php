<?php

require_once __DIR__ . '/../common.php';

function callback($buffer)
{
    return recode("..cp437", $buffer);
}

ob_start("callback");

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://net.pupu.li/icingaweb2/monitoring/list/hosts?format=json',
    CURLOPT_NETRC => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_FAILONERROR => TRUE,
    //CURLOPT_VERBOSE => TRUE,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json"
    ],
]);
$json = curl_exec($ch);
// FIXME process errors

$items = json_decode($json);

$space = '${space}';

echo <<<EOF
#!ipxe
set space:hex 20:20
set space \${space:string}
menu Tilannehuone
item exit Palaa päävalikkoon

EOF;

foreach ($items as $item) {
    print("item {$item->host_name} {$item->host_display_name}\n");
    print("item --gap $space $item->host_output\n");
}

echo <<<EOF
choose --default exit tgt

EOF;

ob_end_flush();
