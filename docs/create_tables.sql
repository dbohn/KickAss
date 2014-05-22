CREATE TYPE turnuesse AS ENUM ('Sommer', 'Winter');

CREATE TABLE Liga (
    id INTEGER NOT NULL,
    name VARCHAR(20) NOT NULL,

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

    PRIMARY KEY(id),
    FOREIGN KEY(saison_id) REFERENCES Saison(id),
    FOREIGN KEY(liga_id) REFERENCES Liga(id),
    FOREIGN KEY(gast_id) REFERENCES Verein(id),
    FOREIGN KEY(gastgeber_id) REFERENCES Verein(id)
);

CREATE TABLE Spieler (
    id INTEGER NOT NULL,
    vorname VARCHAR(30),
    name VARCHAR(30),
    heimatland VARCHAR(30),

    PRIMARY KEY(id)
);

CREATE TABLE Saison (
    id INTEGER NOT NULL,
    turnus TURNUESSE,
    start_datum DATE,
    end_datum DATE,

    PRIMARY KEY(id)
);

CREATE TABLE Verein (
    id INTEGER NOT NULL,
    name VARCHAR(50),
    heimatstadion VARCHAR(100),
    ort VARCHAR(30),

    PRIMARY KEY(id)
);

CREATE TABLE Team (
    id INTEGER NOT NULL,
    name VARCHAR(30),
    verein_id INTEGER NOT NULL,
    saison_id INTEGER NOT NULL,

    PRIMARY KEY(id),
    FOREIGN KEY (verein_id) REFERENCES Verein(id),
    FOREIGN KEY (saison_id) REFERENCES Saison(id)
);

CREATE TABLE spielt_in (
    liga_id INTEGER NOT NULL,
    team_id INTEGER NOT NULL,

    FOREIGN KEY (liga_id) REFERENCES Liga(id),
    FOREIGN KEY (team_id) REFERENCES Team(id)
);

CREATE TABLE spielt_bei (
    team_id INTEGER NOT NULL,
    spieler_id INTEGER NOT NULL,
    trikotnr INTEGER NOT NULL,

    PRIMARY KEY(team_id, spieler_id, trikotnr),
    FOREIGN KEY (team_id) REFERENCES Team(id),
    FOREIGN KEY (spieler_id) REFERENCES Spieler(id),
);

CREATE TABLE erzielt_tor (
    spieler_id INTEGER NOT NULL,
    spiel_id INTEGER NOT NULL,
    spielminute INTEGER NOT NULL,

    PRIMARY KEY(spieler_id, spiel_id, spielminute),
    FOREIGN KEY(spieler_id) REFERENCES Spieler(id),
    FOREIGN KEY(spiel_id) REFERENCES Spiel(id)
);
