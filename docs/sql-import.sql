-- We assume, that in Database(schema) temp we find the base data, that we want to import in our database.
-- To realise this, we would have to change the SQL dump, found on the website, to follow the PostgreSQL syntax and
-- not the MySQL syntax

-- Import of Liga
INSERT INTO Liga 
	SELECT Liga_Nr as id, Liga_Nr || '. Bundesliga' as name FROM temp.Liga;

-- Import of Saison
-- We only know, that in the source DB, there is the current season, but we do need the begin and end date.
-- That's why we have hardcoded these values.
INSERT INTO Saison(start_datum, end_datum, liga) VALUES('2013-08-09', '2014-05-10', 1);
INSERT INTO Saison(start_datum, end_datum, liga) VALUES('2013-07-19', '2014-05-11', 2);
INSERT INTO Saison(start_datum, end_datum, liga) VALUES('2013-07-19', '2014-05-10', 3);

-- Import of Verein
-- As there is absolutely no data about heimatstadion and ort, we'll leave it blank
INSERT INTO Verein(id, name) 
	SELECT V_ID as id, name FROM temp.Verein;

-- Import of Team
-- row_number() OVER() is giving us the count of the fetched row
INSERT INTO Team 
	SELECT row_number() OVER() as id, v.name as name, v.V_ID as verein_id, l.id as liga_id, s.id as saison_id 
		FROM temp.Verein as v, Liga as l, Saison as s WHERE v.Liga = l.id AND s.liga = l.id;

-- Import of Spiele
INSERT INTO Spiel 
	SELECT row_number() OVER() as id, ts.Datum as anpfiff_datum, 'unknown' as ort, -1 as spieldauer, 
		s.id as saison_id, t.liga_id as liga_id, ts.Gast as gast_id, ts.Heim as gastgeber_id, 
		ts.Spieltag as spieltag, ts.Tore_Heim as toreHeim, ts.Tore_Gast as toreGast
		FROM temp.Spiel as ts, Saison as s, Team as t WHERE t.id=ts.Heim AND s.id=t.saison_id;

-- Import of Spieler
INSERT INTO Spieler 
	SELECT Spieler_Id as id, Spieler_Name as name, Land as heimatland FROM temp.Spieler;

-- Import of spielt_bei
INSERT INTO spielt_bei 
	SELECT t.id as team_id, s.Spieler_id as spieler_id, s.Trikot_Nr as trikotnr
		FROM temp.Spieler as s, Team as t WHERE t.verein_id = s.Vereins_ID;

-- Import of erzielt_tore
INSERT INTO erzielt_tore 
	SELECT s.Spieler_ID as spieler_id, t.saison_id as saison_id, s.Tore as anzahl
		FROM temp.Spieler as s, Team as t WHERE t.verein_id = s.Vereins_ID;
