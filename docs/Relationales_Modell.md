#Relationales Modell

Im Folgenden sowohl das vollständige Relationale Modell als auch eine vereinfachte Version zu dem ER-Diagramm.

Entgegen der üblichen Notation mussten wir hier auf das Kennzeichnen der Schlüssel durch Unterstreichen verzichten, stattdessen sind Schlüssel **fett** dargestellt. Fremdschlüssel werden zur Verdeutlichung *kursiv* dargestellt.

##Vollständig

###Entitäten
* Verein: {[**id**, name]}
* Spieler: {[**id**, vorname, name, heimatland, trikotnr]}
* Spiel: {[**id**, anpfiff_datum, ort, spieldauer, saison]}
* Liga: {[**id**, name]}

###Relationen

* spielt-in: {[***id_verein***, *id_liga*, saison]}
* spielt-bei: {[***id_spieler***, *id_verein*, saison]}
* erzielt-Tor {[***id_spieler***, ***id_spiel***, **Spielminute**]}
* ist Gast: {[***id_spiel***, *id_verein*]}
* ist Gastgeber: {[***id_spiel***, *id_verein*]}
* gehört zu: {[***spiel_id***, *id_liga*]}

##Vereinfacht

In dem vereinfachten Modell wurden bereits einige der vereinfachten Beziehungen umbenannt.

###Entitäten
* Verein: {[**id**, name, *id_liga*, saison]}
* Spieler: {[**id**, vorname, name, heimatland, trikotnr, *spielt_bei*, saison]}
* Spiel: {[**id**, anpfiff_datum, ort, spieldauer, saison, *gast*, *gastgeber*, *liga*]}
* Liga: {[**id**, name]}

###Relationen

* gehört zu: {[***spiel_id***, *id_liga*]}
