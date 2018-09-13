# Oikopupu 🐇

Generates simple SNAT/DNAT peering tables for Pupu Network. Used for
routing traffic trough PupuNet even when using public IP addresses and
hostnames.

The objective is to reduce the load on Internet nodes and to provide
fail-safe connectivity to other Internet connected PupuNet hosts.

You need access to the Pupu Assigned Names And Numbers spreadsheet
hosted on Google Sheets to use this application.

NB! You need to ensure all the listed hosts provide the same services (such
as HTTP) to both Pupu and Internet interfaces.

## Installation (web server)

Install PHP, composer, and Google Sheets API:

```sh
sudo apt install composer php-fpm
composer install
```

### Updating dependencies (optional)

If this project gets unmaintained and the dependencies die out, just
update them from `composer.json`:

```sh
composer update
```

Test it and if it works after update, then commit the changed lock
file to this repo.

### Login

Create Google API key and download its credentials. Follow the
instructions at
[https://developers.google.com/sheets/api/quickstart/php#step_1_turn_on_the](Google Sheets PHP Quickstart Guide).

Then and give credentials file as an argument to login script:

```sh
./login /path/to/credentials.json
```

Follow script's instructions. You need to allow the access by opening
the given link using your browser and then copy-paste the key back to
the command line.

### Test in standalone mode

This should output `iptables` chains in text mode:

```sh
./fetch_rules_google
```

### Web service setup

Setup php-fpm as usual. Host `dist/` directory only.

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
