# Installation (client)

These are installation instructions for client (router or standalone
computer on PupuNet). We setup a timer which runs every hour and
updates the peering table.

## Requirements

You need systemd based Linux distribution to use this application. The
following instructions are for Ubuntu and Debian but the same packages
and files are probably available for other distros, too.

Install PHP and its curl library:

```sh
sudo apt install php-cli php-curl
```

And firewall manager if you don't already have one:

```sh
sudo apt install iptables-persistent
```

I recommend using *iptables-persistent* for managing the firewall. If
you are using higher-level rule management tool, then please submit me
a patch so I can include it to this documentation.

## Test the client

The following should print out valid ruleset but not yet apply it:

```sh
./fetch_rules https://example.com/oikopupu
```

## Iptables setup

You need extra chains which are managed by Oikopupu:

* PUPU_FILTER at *filter* table. It whitelists Pupu hosts which have
  also Internet connectivity.
* PUPU_DNAT at *nat* table. It performs DNAT from Internet IP address
  to PupuNet address.

Take [rules.v4](rules.v4) file as an example and edit
`/etc/iptables/rules.v4`. After editing, apply the new rules by
running:

```sh
sudo netfilter-persistent start
```

Install systemd job by copying the example [systemd
unit](oikopupu.service) and [timer](oikopupu.timer) to your system:

```sh
cp oikopupu.service oikopupu.timer /etc/systemd/system/
```

Then edit the file by changing installation path, user name, and
Oikopupu server URL to the job. Then reload, enable, and start it:

```sh
sudo systemctl daemon-reload
sudo systemctl enable oikopupu.timer
sudo systemctl start oikopupu.timer
```

Now it should work!

## Testing

Validate it by running traceroute to any of the peered hosts. If
working, you should get one or two hops only:

	$ traceroute instanssi.org
	traceroute to instanssi.org (217.112.252.52), 30 hops max, 60 byte packets
	 1  jkl.hacklab.fi (217.112.252.52)  1.332 ms  1.252 ms  2.645 ms
	 2  jkl.hacklab.fi (217.112.252.52)  41.828 ms *  41.685 ms

Yeah!
