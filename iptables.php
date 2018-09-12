<?php

function iptables_format($output) {
    ob_start();
    
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

    return ob_get_clean();
}

function drop_skip($array, $skip_str) {
    // Assuming input from `hostname -I` which is list of IP addresses
	// delimited by spaces. Creating array from them.
    $skip = empty($skip_str) ? [] : explode(" ", $skip_str);

    return array_filter($array, function($a) use ($skip) {
        // Skip own address if matches to either Pupu or Internet address
        return
            array_search($a['inet_ipv4'], $skip, TRUE) === FALSE &&
            array_search($a['pupu_ipv4'], $skip, TRUE) === FALSE;
    });
}
