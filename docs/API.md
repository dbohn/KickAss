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

###Verein `api/list/verein`

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

* Parameter:
    * Liga: `api/list/spieltag/<liga>`, `<liga>=1|2|3`
* Rückgabe: `[{"id": ..., "name" : ....},...]`
* Bemerkungen: -

##Antworten

TBD
