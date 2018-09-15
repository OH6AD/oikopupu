## Installation (client)

Install PHP and curl library

```sh
sudo apt install php-cli php-curl
```

### Run

On client:

```sh
./fetch_rules https://example.com/oikopupu | sudo iptables-restore --noflush
```

TODO systemd job etc.
