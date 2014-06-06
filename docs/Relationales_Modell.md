#Relationales Modell

Im Folgenden sowohl das vollständige Relationale Modell als auch eine vereinfachte Version zu dem ER-Diagramm.

Entgegen der üblichen Notation mussten wir hier auf das Kennzeichnen der Schlüssel durch Unterstreichen verzichten, stattdessen sind Schlüssel **fett** dargestellt. Fremdschlüssel werden zur Verdeutlichung *kursiv* dargestellt.

##Vollständig

###Entitäten
* Verein: {[**id**, name, ort, heimatstadion]}
* Team: {[**id**]}
* Spieler: {[**id**, vorname, name, heimatland]}
* Spiel: {[**id**, anpfiff_datum, ort, spieldauer, toreGast, toreHeim]}
* Liga: {[**id**, name]}
* Saison: {[**id**, start_datum, end_datum]}

###Relationen

* spielt-in: {[***id_team***, ***id_liga***]}
* spielt-bei: {[***id_team***, ***id_spieler***, **trikotnummer**]}
* erzielt-Tor {[***id_spieler***, ***id_saison***, anzahl]}
* ist Gast: {[***id_spiel***, *id_verein*]}
* ist Gastgeber: {[***id_spiel***, *id_verein*]}
* gehört zu: {[***spiel_id***, *id_liga*]}
* spielt-fuer: {[***id_team***, *id_verein*]}
* findet-statt: {[***id_spiel***, *id_saison*, spieltag]}
* spielt-waehrend: {[***id_team***, *id_saison*]}

##Vereinfacht

In dem vereinfachten Modell wurden bereits einige der vereinfachten Beziehungen umbenannt.

###Entitäten
* Verein: {[**id**, name, ort, heimatstadion]}
* Team: {[**id**, *verein*, *saison*, *liga*]}
* Spieler: {[**id**, vorname, name, heimatland]}
* Spiel: {[**id**, anpfiff_datum, ort, spieldauer, toreGast, toreHeim, *gast*, *gastgeber*, *liga*, *saison*, spieltag]}
* Liga: {[**id**, name]}
* Saison: {[**id**, start_datum, end_datum]}

###Relationen

* spielt-bei: {[***id_team***, ***id_spieler***, **trikotnummer**]}
* erzielt-Tor {[***id_spieler***, ***id_saison***, anzahl]}
