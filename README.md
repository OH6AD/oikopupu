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

## Installation

Install PHP, composer, and Google Sheets API:

```sh
sudo apt install composer
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

## Login

Create Google API key, download its credentials and give credentials
as an argument to login script:

```sh
./login /path/to/credentials.json
```

Follow its instructions. You need to allow the access using your
browser and then copy-paste the given key back to the command line.

## Running

### As standalone

```sh
format=iptables skip="`hostname -I`" ./iptables.php | sudo iptables-save --noflush
```

### As web service

TODO
