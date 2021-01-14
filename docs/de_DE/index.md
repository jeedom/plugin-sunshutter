# Shutter Management Plugin

# Description

Mit diesem Plugin können Sie die Position Ihrer Fensterläden einfacher entsprechend der Sonnenposition verwalten. Dieses Plugin funktioniert vollständig lokal und erfordert keine externe Verbindung.

Sie können finden [hier](https://www.jeedom.com/blog/?p=4310) Ein Artikel mit einer Beispielkonfiguration des Plugins.

# Plugins Konfiguration

Hier gibt es nichts Besonderes, nur um das Plugin zu installieren und zu aktivieren.

## Wie es funktioniert ?

Das Plugin passt die Position Ihrer Fensterläden relativ zu den Sonnenpositionen (Azimut und Höhe) je nach Zustand an.

# Konfiguration der Rollläden

Die Konfiguration ist in mehrere Registerkarten unterteilt :

## Equipement

Auf der ersten Registerkarte finden Sie die gesamte Konfiguration Ihrer Geräte :

- **Name der Ausrüstung** : Name Ihrer Ausrüstung.
- **Übergeordnetes Objekt** : Gibt das übergeordnete Objekt an, zu dem das Gerät gehört.
- **Kategorie** : Mit dieser Option können Sie die Kategorie Ihrer Ausrüstung auswählen.
- **Aktivieren** : macht Ihre Ausrüstung aktiv.
- **Sichtbar** : macht Ihre Ausrüstung auf dem Armaturenbrett sichtbar.

## Configuration

### Configuration

- **Überprüfung** : Häufigkeit der Überprüfung der Bedingungen und Position der Klappen.
- **Gewinnen Sie die Kontrolle zurück** : verhindert, dass das Verschlussmanagementsystem seine Position ändert, wenn es manuell bewegt wurde. Beispiel : Das System schließt den Verschluss, Sie öffnen ihn, er wird nicht mehr berührt, bis der Befehl "Resume Management" ausgelöst wird oder die Zeit für die Übernahme der Kontrolle abgelaufen ist.
- **Breite** : der Breitengrad Ihres Verschlusses / Hauses.
- **Länge** : die Länge Ihres Verschlusses / Hauses.
- **Höhe** : die Höhe Ihres Verschlusses / Hauses.
- **Verschlusszustand** : Befehl zur Anzeige der aktuellen Position des Verschlusses.
- **Verschlussposition** : Steuerung zum Positionieren der Klappe.
- **Verschlussposition aktualisieren (optional)** : Befehl zum Aktualisieren der Position des Verschlusses.
- **Maximale Zeit für eine Reise** : Zeit für eine vollständige Bewegung (auf und ab oder auf und ab) in Sekunden.

## Condition

- **Handlungsbedingung** : Wenn diese Bedingung nicht erfüllt ist, ändert das Plugin die Position des Fensters nicht.
- **Der Moduswechsel bricht ausstehende Suspensionen ab** : Wenn diese Option aktiviert ist, wird der Verschluss durch eine Änderung des Modus wieder automatisch verwaltet.
- **Sofortmaßnahmen sind systematisch und vorrangig** : Wenn diese Option aktiviert ist, werden die Sofortaktionen ausgeführt, auch wenn sie ausgesetzt sind und die Reihenfolge der Bedingungen nicht berücksichtigt wird.

In der Bedingungstabelle können Sie bestimmte Positionierungsbedingungen angeben, die die Klappenpositionstabelle erfassen :
- **Position** : Wenn die Bedingung erfüllt ist, die Position des Verschlusses.
- **Modus** : Die Bedingung funktioniert nur, wenn sich der Verschluss in diesem Modus befindet (Sie können mehrere durch Kommas getrennte Zeichen setzen ``,``). Wenn dieses Feld nicht ausgefüllt ist, wird die Bedingung unabhängig vom Modus getestet.

>**Wichtig**
>
>Wir sprechen hier über den Verschlussmodus, der mit dem Modus-Plugin NICHTS ZU SEHEN hat

- **Sofortige Aktion** : wirkt sofort, sobald die Bedingung erfüllt ist (wartet daher nicht auf die Überprüfung cron).
- **Anhalten** : Wenn die Bedingung erfüllt ist, wird die automatische Verwaltung des Verschlusses unterbrochen.
- **Zustand** : Ihr Zustand.
- **Kommentar** : freie Felder für Kommentare.

## Positionnement

- **% Öffnung** : die%, wenn der Verschluss geöffnet ist.
- **% schließen** : die%, wenn der Verschluss geschlossen ist.
- **Standardaktion** : Die Standardaktion, wenn keine Bedingung und Position gültig ist.

Hier können Sie die Positionierung des Verschlusses entsprechend der Sonnenposition steuern.

- **Azimut** : Sonnenstandwinkel.
- **Höhe** : Höhenwinkel der Sonne.
- **Position** : Position des Verschlusses, wenn die Sonne im Azimut und in den Höhengrenzen steht.
- **Zustand** : Zusätzliche Bedingung, die erfüllt sein muss, damit der Verschluss diese Position einnimmt (kann leer sein).
- **Kommentar** : freie Felder für Kommentare.

>**TIPP**
>
>Kleiner Tipp die Seite [suncalc.org](https://www.suncalc.org) Sobald Ihre Adresse eingegeben wurde, können Sie den Sonnenstand (und damit die Winkel Azimut und Höhe) entsprechend den Tagesstunden anzeigen (ziehen Sie einfach die kleine Sonne nach oben).

## Planning

Hier sehen Sie die in der Agenda-Planung erstellten Verschlusspositionierungspläne.

## Commandes

- **Sonnenazimut** : aktueller Azimutwinkel der Sonne.
- **Sonnenaufgang** : aktueller Elevationswinkel der Sonne.
- **Aktion erzwingen** : Erzwingt die Berechnung der Verschlussposition entsprechend dem Sonnenstand und den Bedingungen und wendet das Ergebnis unabhängig vom Verwaltungszustand (angehalten oder nicht) darauf an).
- **Letzte Position** : Letzte vom Plugin vom Verschluss angeforderte Position.
- **Managementstatus** : Managementstatus (ausgesetzt oder nicht).
- **Zusammenfassung** : Erzwingt, dass die Verwaltung in den automatischen Modus zurückgesetzt wird (beachten Sie, dass dieser Befehl gestartet werden muss, um zur automatischen Verwaltung zurückzukehren, wenn Sie die Position Ihres Verschlusses manuell geändert und die Option "Kontrolle nicht zurücknehmen" aktiviert haben").
- **Anhalten** : Unterbricht die automatische Verschlusspositionierung.
- **Aktualisieren** : Aktualisieren Sie die Werte der Befehle "Sonnenazimut" und "Sonnenhöhe"".
- **Modus** : aktueller Verschlussmodus.

Sie können "Modus" -Befehle hinzufügen. Der Befehlsname ist der Modusname.

# Panel

Das Plugin verfügt über ein Management Panel für Desktop und Mobile. Um es zu aktivieren, gehen Sie einfach zu Plugins → Plugins-Verwaltung, klicken Sie auf das Plugin zur Fensterverwaltung und aktivieren Sie unten rechts die Kontrollkästchen, um die Desktop- und Mobile-Panels anzuzeigen.
