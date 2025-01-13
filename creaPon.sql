
DROP TABLE IF EXISTS PARTICIPER;
DROP TABLE IF EXISTS ASSISTER;
DROP TABLE IF EXISTS RAJOUTER;
DROP TABLE IF EXISTS SEANCE;
DROP TABLE IF EXISTS CLIENT;
DROP TABLE IF EXISTS MONITEUR;
DROP TABLE IF EXISTS HISTORIQUE;
DROP TABLE IF EXISTS PONEY;
DROP TABLE IF EXISTS COURS;
DROP TABLE IF EXISTS PERSONNE;

CREATE TABLE ASSISTER (
  idCl           INTEGER NOT NULL,
  idSeance       INTEGER NOT NULL,
  statutPayement TEXT,
  restePayement  INTEGER,
  PRIMARY KEY (idCl, idSeance),
  FOREIGN KEY (idCl) REFERENCES CLIENT (idCl),
  FOREIGN KEY (idSeance) REFERENCES SEANCE (idSeance)
);

CREATE TABLE CLIENT (
  idCl            INTEGER NOT NULL,
  idPersonne      INTEGER NOT NULL,
  dateDeNaissance DATE,
  niveau          TEXT,
  cotisation      TEXT,
  PRIMARY KEY (idCl, idPersonne),
  FOREIGN KEY (idPersonne) REFERENCES PERSONNE (idPersonne)
);

CREATE TABLE MONITEUR (
  idMoniteur      INTEGER NOT NULL,
  idPersonne      INTEGER NOT NULL,
  dateDeNaissance DATE,
  PRIMARY KEY (idMoniteur, idPersonne),
  FOREIGN KEY (idPersonne) REFERENCES PERSONNE (idPersonne)
);

CREATE TABLE COURS (
  idCours   INTEGER PRIMARY KEY ,
  typeCours TEXT
);

CREATE TABLE HISTORIQUE (
  idSuivi          INTEGER PRIMARY KEY ,
  dateAchat        DATE,
  descriptionAchat TEXT
);

CREATE TABLE PARTICIPER (
  idSeance INTEGER NOT NULL,
  idPoney  INTEGER NOT NULL,
  idCl  INTEGER NOT NULL,
  PRIMARY KEY (idSeance, idPoney, idCl),
  FOREIGN KEY (idPoney) REFERENCES PONEY (idPoney),
  FOREIGN KEY (idSeance) REFERENCES SEANCE (idSeance)
);

CREATE TABLE PERSONNE (
  idPersonne INTEGER PRIMARY KEY,
  prenom     TEXT,
  nom        TEXT,
  numTel     INTEGER UNIQUE,
  email      TEXT UNIQUE,
  poids      REAL,
  mdp        TEXT,
  est_admin BOOLEAN
);

CREATE TABLE PONEY (
  idPoney  INTEGER PRIMARY KEY ,
  nomP     TEXT,
  poidsMax REAL,
  imagePoney TEXT
);

CREATE TABLE RAJOUTER (
  idCl    INTEGER NOT NULL,
  idSuivi INTEGER NOT NULL,
  PRIMARY KEY (idCl, idSuivi),
  FOREIGN KEY (idCl) REFERENCES CLIENT (idCl),
  FOREIGN KEY (idSuivi) REFERENCES HISTORIQUE (idSuivi)
);

CREATE TABLE SEANCE (
  idSeance      INTEGER PRIMARY KEY ,
  dateDebut     DATE,
  dateFin       DATE,
  duree         INTEGER,
  particulier   BOOLEAN,
  nbPersonneMax INTEGER,
  niveau        TEXT,
  idMoniteur    INTEGER NOT NULL,
  idCours       INTEGER NOT NULL,
  FOREIGN KEY (idCours) REFERENCES COURS (idCours)
);

-- Trigger: Vérification du nombre maximum de participants
--CREATE TRIGGER VerifNbParticipants
--BEFORE INSERT ON PARTICIPER
--WHEN (SELECT COUNT(*) FROM PARTICIPER WHERE idSeance = NEW.idSeance) >= 
--     (SELECT nbPersonneMax FROM SEANCE WHERE idSeance = NEW.idSeance)
--BEGIN
--  SELECT RAISE(ABORT, 'Nombre maximum de participants atteint pour cette séance.');
--END;

-- Trigger: Vérification de la durée du cours
--CREATE TRIGGER VerifDureeCours
--BEFORE INSERT ON SEANCE
--WHEN NEW.duree NOT IN (1, 2)
--BEGIN
--  SELECT RAISE(ABORT, 'La durée du cours doit être de 1 ou 2 heures.');
--END;

