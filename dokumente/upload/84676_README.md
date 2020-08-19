# Cookie Management

In diesem Repository liegt das komplette Softwarepaket, das zur Verwaltung von Cookie-Richtlinien und zu integrierender Skripte benötigt wird.

## Abhängigkeiten installieren

Nachdem das Respository geklont wurde, müssen zusätzliche Pakete durch Composer installiert werden.

```bash
$ cd www
$ composer install
```

## LESS zu CSS kompilieren

Der Einstiegspunkt des Layouts befindet sich in der Datei [theme.less](www/less/theme.less `LESS-Einstiegspunkt`).
Diese Datei muss kompiliert und die resultierende Datei ins [CSS-Verzeichnis](www/public/css) geschrieben werden.

```bash
$ sudo npm install -g less
$ sudo chmod -R a+rwX ../cookie-consent-management
$ cd www/less
$ lessc --source-map theme.less ../public/css/theme.css
```

Beide Schritte können auch über die Makefile erledigt werden. Diese schreibt zusätzlich die Git-Commit-ID in die config/version-id.txt, damit erkannt werden kann, ob ein Update vorliegt.

```bash
$ make
```

## Update-ZIP erzeugen

Ein ZIP für Updates kann mit der Makefile erstellt werden:

```bash
$ make zip
```

Die Datei `ccm.zip` muss dann auf update.ccm19.de hochgeladen werden.

## Setup-Datei erzeugen

Ein Selbstextrahierendes PHP-Setup zur Veröffentlichung  kann mit der Makefile erstellt werden:

```bash
$ make setup
```

Die Datei `ccm19-setup.php` kann danach verteilt werden.

ACHTUNG: Das Setup-Archiv enthält, anders als das Update-ZIP auch die Dateien in var/ außer `cm-config.json` `cm-consent-protocol.log` `cm-found-*.json`!

## Übersetzungen aktualisieren

Mit `translation:update` können alle neu hinzugekommenen `{% trans %}{% endtrans %}` und `TranslatorInterface::trans()` als Platzhalter in die Übersetzungsdateien übernommen werden.

Für Deutsch (de) sieht das Kommando folgendermaßen aus:

```bash
$ php bin/console translation:update --force de --output-format=yaml
```

## Logdatei erzeugen um Changelog zu schreiben
```bash
git log --all --pretty=format:"%h - %an, %cd : %s" > ../commit.log
```
