CREATE TABLE Liga (
    id INTEGER NOT NULL,
    name VARCHAR(20) NOT NULL,

    PRIMARY KEY(id)
);

CREATE TABLE Saison (
    id SERIAL PRIMARY KEY,
    start_datum DATE,
    end_datum DATE,
    liga INTEGER NOT NULL,

    FOREIGN KEY(liga) REFERENCES Liga(id)
);

CREATE TABLE Verein (
    id INTEGER NOT NULL,
    name VARCHAR(50),
    heimatstadion VARCHAR(100),
    ort VARCHAR(30),

    PRIMARY KEY(id)
);


CREATE TABLE Spiel (
    id INTEGER NOT NULL,
    anpfiff_datum TIMESTAMP NOT NULL,
    ort VARCHAR(100) NOT NULL,
    spieldauer SMALLINT NOT NULL,
    saison_id INTEGER NOT NULL,
    liga_id INTEGER NOT NULL,
    gast_id INTEGER NOT NULL,
    gastgeber_id INTEGER NOT NULL,
    spieltag INTEGER NOT NULL,
    toreHeim INTEGER NOT NULL,
    toreGast INTEGER NOT NULL,

    PRIMARY KEY(id),
    FOREIGN KEY(saison_id) REFERENCES Saison(id),
    FOREIGN KEY(liga_id) REFERENCES Liga(id),
    FOREIGN KEY(gast_id) REFERENCES Verein(id),
    FOREIGN KEY(gastgeber_id) REFERENCES Verein(id)
);

CREATE TABLE Spieler (
    id INTEGER NOT NULL,
    name VARCHAR(120),
    heimatland VARCHAR(30),

    PRIMARY KEY(id)
);


CREATE TABLE Team (
    id INTEGER NOT NULL,
    name VARCHAR(30),
    verein_id INTEGER NOT NULL,
    saison_id INTEGER NOT NULL,
    liga_id INTEGER NOT NULL,

    PRIMARY KEY(id),
    FOREIGN KEY (verein_id) REFERENCES Verein(id),
    FOREIGN KEY (saison_id) REFERENCES Saison(id),
    FOREIGN KEY (liga_id) REFERENCES Liga(id)
);

CREATE TABLE spielt_bei (
    team_id INTEGER NOT NULL,
    spieler_id INTEGER NOT NULL,
    trikotnr INTEGER NOT NULL,

    PRIMARY KEY(team_id, spieler_id, trikotnr),
    FOREIGN KEY (team_id) REFERENCES Team(id),
    FOREIGN KEY (spieler_id) REFERENCES Spieler(id)
);

CREATE TABLE erzielt_tore (
    spieler_id INTEGER NOT NULL,
    saison_id INTEGER NOT NULL,
    anzahl INTEGER NOT NULL,

    PRIMARY KEY(spieler_id, saison_id),
    FOREIGN KEY(spieler_id) REFERENCES Spieler(id),
    FOREIGN KEY(saison_id) REFERENCES Saison(id)
);
