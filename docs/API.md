KickAss API
===========

Für den Zugriff auf die Daten stellt KickAss eine REST-API bereit. Die Antworten werden im JSON-Format  ausgeliefert und haben den folgenden Aufbau:

```
{
  "status": "ok" | "error",
  "sql": ...,
  "payload": [
        {
          ...
        },
        {
          ...
        },
        ...
    ]
}
```

* `status` gibt an, ob die Abfrage erfolgreich war (`ok`), oder ob ein Fehler aufgetreten ist (`error`)
* `sql` beinhaltet das verwendete Query (bei Antworten) oder `null` bei Listen und Fehlern
* `payload` beinhaltet bei Fehlern die Fehlermeldung oder im Erfolgsfall ein Array von Daten-Objekten

#Arten von Abfragen

Die API unterstützt grundsätzlich zwei verschiedene Arten von Abfragen:

* Listen: Gibt eine Liste von Key-Value Paaren zurück. Die Keys können für weitere Anfragen an die API verwendet werden.

* Antworten: Liefert die Antwort auf eine der in der Projektbeschreibung definierten Fragen.


##Listen

###Saison: `api/list/saison`

Listet alle Saisons auf.

* Parameter: -
* Rückgabe: `[{"id": ..., "name" : ....},...]`
* Bemerkungen: Die Id hat das Format `Liga.Datum` und dient zur eindeutigen Identifizierung einer Saison

###Verein: `api/list/verein`

Listet alle Vereine auf. Wird keine Liga übergeben, werden alle Vereine aufgelistet, ansonsten nur die
Verein, die in der übergebenen Liga spielen.

* Parameter:
    * Liga: `api/list/verein/<liga>`, `<liga>=1|2|3`
* Rückgabe: `[{"id": ..., "name" : ....},...]`
* Bemerkungen: -

###Liga: `api/list/liga`

Listet alle Ligen auf.

* Parameter: -
* Rückgabe: `[{"id": ..., "name" : ....},...]`
* Bemerkungen: -

###Spieltag: `api/list/spieltag`

Listet Spieltage auf. Wird keine Liga übergeben, wird die maximale Anzahl von Spieltagen aller Ligen zurückgegeben. Wird eine Liga übergeben werden die Spieltag der Liga ausgegeben.

* Parameter: `api/list/spieltag/<liga>`
    * `<liga>=1|2|3`
* Rückgabe: `[{"id": ..., "name" : ....},...]`
* Bemerkungen: -

##Antworten

###Erstes Spiel: `api/games/firstgame`

Beantwortung der Fragestellung:

_An welchem Tag fand das erste Spiel in dieser Saison statt?_

* Parameter: `api/games/firstgame/<saison_id>`
    * `saison_id` muss dem Format der Saison IDs (siehe `api/list/saison`) entsprechen
* Rückgabe: `[{"anpfiff_datum": ...}]`



###Spiele am Spieltag: `api/games/spieltag`

Beantwortung der Fragestellung möglich:

_Zeige die Daten aller Spiele an, die am ersten Spieltag aller drei Ligen nach 17 Uhr
begonnen haben._

* Parameter: `api/games/spieltag/<stag>[/<stunde>/<minute>]`:  
    * `<stag>` der Spieltag (natürliche Zahl)
    * `<stunde>` Stundenangabe: 0-23
    * `<minute>` Minutenangabe: 0-59
* Rückgabe:

```
[{
            "anpfiff_datum": "...",
            "liga": "...",
            "heim": "...",
            "gast": "...",
            "toreheim": ...,
            "toregast": ...
},...]
```
* Bemerkungen: Wird keine Uhrzeit angegeben, so werden alle Spiele an diesem Spieletag zurückgegeben. Bei Angabe einer Uhrzeit werden alle Spiele zurückgegeben, die __genau__ oder __nach__ der angegebenen Zeit beginnen.



###Spieler und Tore: `api/player/playergoals`

Beantwortung der Fragestellung möglich:

_Welche Spieler haben in dieser Saison bereits mehr als fünf Tore geschossen?_

* Parameter:
    * Tore: `api/player/playergoals/tore`: `tore` muss eine natürliche Zahl sein
* Rückgabe: `[{"name": ..., "anzahl": ...}, ...]`



###Spieler nach Team: `api/player/team`

Beantwortung der Fragestellung möglich:

_Welche Spieler spielen für den Verein “FC Bayern München“? Gib auch die Trikotnummer
und das Heimatland jedes Spielers sowie die Anzahl seiner Tore mit aus. Ordne die
Ergebnisse aufsteigend nach der Trikotnummer._

* Parameter: `api/player/team/<id>`:  
    * `<id>`: ID des Vereins (wie sie bspw. von `/api/list/verein` bereitgestellt wird)
* Rückgabe:

```
[{
            "name": "...",
            "heimatland": "...",
            "tore": ...,
            "trikotnr": ...
},...]
```
* Bemerkungen: Die Ergebnisse sind aufsteigend nach Trikotnummer geordnet.



###Anzahl der gewonnen Spiele: `/api/team/won`

Beantwortung der Fragestellung möglich:

_Wie viele Spiele hat „Hannover 96“ bis heute gewonnen?_

* Parameter: `api/team/won/<id>`:  
    * `<id>`: ID des Vereins (wie sie bspw. von `/api/list/verein` bereitgestellt wird)
* Rückgabe: `[{"gewonnen": ...}]`
* Bemerkungen: Die Ergebnisse sind aufsteigend nach Trikotnummer geordnet.


###Verlierer: `/api/team/loser`

Beantwortung der Fragestellung möglich:

_Gesucht sind Vereinsname, Spieler_ID, Trikotnummer und Name aller Spieler, die für den
Verein spielen, der in dieser Saison die meisten Niederlagen erlitten hat (auch mehrere
Vereine mit gleicher Anzahl möglich)._

* Parameter: -
* Rückgabe:

```
[{
            "id": ...,
            "name": "...",
            "verloren": ...,
            "trikotnr": ...,
            "spieler_id": ...,
            "spielername": "..."
},...]
```

## Komplette Ergebnistabelle

Neben der reinen Beantwortung der Fragen bietet die KickAss API noch eine komplette Ergebnistabelle bereit, wie sie in der Bundesliga übrig ist:

### Tabelle: `api/tabelle`

* Parameter: `api/tabelle[/<liga>]`
    * `<liga>`: Liga (`1|2|3`)
* Rückgabe:

```
[{
            "id": ...,
            "name": "...",
            "spieltage": ...,
            "gewonnen": ...,
            "unentschieden": ...,
            "verloren": ...,
            "tore": ...,
            "gegentore": ...,
            "diff": ...,
            "punkte": ...
},...]
```
* Bemerkung: Die Ergebnisse sind absteigend nach Punkten sortiert


## Arff Daten und Datenexport

Für das Data Mining kann eine Arff-Datei für Weka erzeugt und exportiert werden, mit der dann entsprechend ein Klassifikator gelernt werden kann.

### Arff Daten: `api/arff`

* Parameter: `api/arff[/download]`
    * `download`: Wird dieser Parameter mit angegeben, wird die reine Arff Datei zum Download angeboten.
* Rückgabe: Im `payload` Feld befindet sich die Arff Datei.
