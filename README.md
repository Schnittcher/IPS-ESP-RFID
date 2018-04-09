<a href="https://www.symcon.de"><img src="https://img.shields.io/badge/IP--Symcon-4.0-blue.svg?style=flat-square"/></a>
<a href="https://www.symcon.de"><img src="https://img.shields.io/badge/IP--Symcon-5.0-blue.svg?style=flat-square"/></a>
<br />

# IPS-ESP-RFID
Mit diesem Modul ist es möglich, geflashte ESPs mir der ESP-RFID Firmware in IP-Symcon zu integrieren.

## Inhaltverzeichnis
1. [Voraussetzungen](#1-voraussetzungen)
2. [Installation](#2-installation)
4. [Konfiguration in IP-Symcon](#3-konfiguration-in-ip-symcon)

## 1. Voraussetzungen

* [Websocket Client](https://github.com/Nall-chan/IPSNetwork)
* mindestens IPS Version 4.3

## 2. Installation

Websocket Client:
```
git://github.com/Nall-chan/IPSNetwork.git
```

IPS-RFIDLogger:
```
git://github.com/Nall-chan/IPSNetwork.git
```

## 3. Konfiguration in IP-Symcon

### Websocket Client

URL: ws://IPDesESPs/ws

Optionale HTTP Basis Authentifizierung:

Benutzername: admin

Passwort: Passwort vom Webinterface (ESP-RFID), Standardpasswort ist admin

Die restlichen Einstellungen bleiben auf dem Standardwert.

### IPS-RFIDlogger
Die Liste, beinhaltet alle Transponder IDs, die das Relais schalten dürfen.
Um die Liste zu füllen, den Chip / die Karte vor den Empfänger halten und danach in dem Konfigurationsformular den Button "Add last Transponder" anklicken. 