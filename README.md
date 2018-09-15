# Oikopupu 🐇

Generates simple SNAT/DNAT peering tables for Pupu Network. Used for
routing traffic trough PupuNet even when using public IP addresses and
hostnames.

The objective is to reduce the load on Internet nodes and to provide
fail-safe connectivity to other Internet connected PupuNet hosts.

You need access to the Pupu Assigned Names And Numbers spreadsheet
hosted on Google Sheets to run the server side part of this
application. For running the client you only need URL of the web
service.

NB! You need to ensure all the peered hosts provide the same services
(such as HTTP) to both Pupu and Internet interfaces.

## Setup

* [Server](doc/server.md) which reads the spreadsheet and provides a
  web service where to get the peering data from.
* [Client](doc/client.md) which is a router or standalone computer
  connected to PupuNet wanting to benefit from peering.

## What is PupuNet?

Pupu Network (PupuNet) is voluntary radio amateur / hackerspace network
operated in Central Finland. It consists of license free 5.6GHz radio
links. There's a
[slideset in Finnish](https://docs.google.com/presentation/d/1mcKpEr5pNB9fg6KunDP3IEJoJpMuC9Em-vqjP4xayTQ/present)
explaining the concept.
