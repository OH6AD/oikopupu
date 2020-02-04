<?php

require_once __DIR__ . '/../common.php';

function callback($buffer)
{
    return recode("..cp437", $buffer);
}

ob_start("callback");

if (array_key_exists("host", $_GET)) {
    $host = $_GET['host'];
    $host_url = urlencode($host);
    $url = "https://net.pupu.li/icingaweb2/monitoring/list/services?host=$host_url&format=json";
    $menu = "Tilannehuone - $host";
} else {
    $host = null;
    $url = 'https://net.pupu.li/icingaweb2/monitoring/list/hosts?format=json';
    $menu = "Tilannehuone";
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
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
:icingastart
set space:hex 20:20
set space \${space:string}
menu $menu
item xexit Takaisin...
item --gap

EOF;

if ($host) {
    foreach ($items as $item) {
        print("item icingastart {$item->service_display_name}\n");
        print("item --gap $space $item->service_output\n");
    }
} else {
    foreach ($items as $item) {
        print("item {$item->host_name} {$item->host_display_name}\n");
        print("item --gap $space $item->host_output\n");
    }
}

echo <<<EOF
choose --default xexit tgt
goto \${tgt} || chain --autofree icinga?host=\${tgt}
goto icingastart
:xexit

EOF;

ob_end_flush();
