CREATE TABLE ASSISTER (
  PRIMARY KEY (idCl, idSeance),
  idCl           int(8) NOT NULL,
  idSeance       int(8) NOT NULL,
  statutPayement VARCHAR(20),
  restePayement  int(42)
);

CREATE TABLE CLIENT (
  PRIMARY KEY (idCl, idPersonne),
  idCl            int(8) NOT NULL,
  idPersonne int(8) NOT NULL,
  dateDeNaissance DATE,
  niveau          VARCHAR(42),
  cotisation      VARCHAR(42)
);

CREATE TABLE COURS (
  PRIMARY KEY (idCours),
  idCours   int(8) NOT NULL,
  typeCours VARCHAR(42)
);

CREATE TABLE HISTORIQUE (
  PRIMARY KEY (idSuivi),
  idSuivi          int(8) NOT NULL,
  dateAchat        DATE,
  descriptionAchat VARCHAR(42)
);

CREATE TABLE PARTICIPER (
  PRIMARY KEY (idSeance, idPoney),
  idSeance int(8) NOT NULL,
  idPoney  int(8) NOT NULL
);

CREATE TABLE PERSONNE(
  PRIMARY KEY (idPersonne),
  idPersonne int(8) NOT NULL,
  prenom     VARCHAR(255),
  nom        VARCHAR(255),
  numTel     int(10),
  email      VARCHAR(255),
  poids      DECIMAL(10,2)
);

CREATE TABLE PONEY (
  PRIMARY KEY (idPoney),
  idPoney  int(8) NOT NULL,
  nomP     VARCHAR(255),
  poidsMax DECIMAL(10,2)
);

CREATE TABLE RAJOUTER (
  PRIMARY KEY (idCl, idSuivi),
  idCl    int(8) NOT NULL,
  idSuivi int(8) NOT NULL
);

CREATE TABLE SEANCE (
  PRIMARY KEY (idSeance),
  idSeance      int(8) NOT NULL,
  dateDebut     DATE,
  dateFin       DATE,
  duree         int(1),
  particulier   boolean,
  nbPersonneMax int,
  niveau        VARCHAR(42),
  idMoniteur    int(8) NOT NULL,
  idCours       int(8) NOT NULL
);

ALTER TABLE CLIENT ADD FOREIGN KEY (idPersonne) REFERENCES PERSONNE (idPersonne);


ALTER TABLE ASSISTER ADD FOREIGN KEY (idSeance) REFERENCES SEANCE (idSeance);
ALTER TABLE ASSISTER ADD FOREIGN KEY (idCl) REFERENCES CLIENT (idCl);

ALTER TABLE PARTICIPER ADD FOREIGN KEY (idPoney) REFERENCES PONEY (idPoney);
ALTER TABLE PARTICIPER ADD FOREIGN KEY (idSeance) REFERENCES SEANCE (idSeance);

ALTER TABLE RAJOUTER ADD FOREIGN KEY (idSuivi) REFERENCES HISTORIQUE (idSuivi);
ALTER TABLE RAJOUTER ADD FOREIGN KEY (idCl) REFERENCES CLIENT (idCl);

ALTER TABLE SEANCE ADD FOREIGN KEY (idCours) REFERENCES COURS (idCours);

delimiter |
CREATE OR REPLACE TRIGGER VerifNbParticipants
BEFORE INSERT ON PARTICIPER
FOR EACH ROW
BEGIN
  DECLARE maxPersonnes INT;
  SELECT nbPersonneMax INTO maxPersonnes FROM SEANCE WHERE idSeance = NEW.idSeance;
  IF (SELECT COUNT(*) FROM PARTICIPER WHERE idSeance = NEW.idSeance) >= maxPersonnes THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Nombre maximum de participants atteint pour cette séance.';
  END IF;
END|
delimiter ;

delimiter |
CREATE OR REPLACE TRIGGER VerifNbParticipants
BEFORE UPDATE ON PARTICIPER
FOR EACH ROW
BEGIN
  DECLARE maxPersonnes INT;
  SELECT nbPersonneMax INTO maxPersonnes FROM SEANCE WHERE idSeance = NEW.idSeance;
  IF (SELECT COUNT(*) FROM PARTICIPER WHERE idSeance = NEW.idSeance) >= maxPersonnes THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Nombre maximum de participants atteint pour cette séance.';
  END IF;
END|
delimiter ;

delimiter |
CREATE OR REPLACE TRIGGER VerifReposPoney
BEFORE INSERT ON PARTICIPER
FOR EACH ROW
BEGIN
    DECLARE dernierCoursFin DATETIME;
    DECLARE dureeTotale INT;

    -- Trouver la fin du dernier cours et la durée totale des cours consécutifs
    SELECT MAX(S.dateFin)
    INTO dernierCoursFin
    FROM SEANCE S
    JOIN PARTICIPER P ON S.idSeance = P.idSeance
    WHERE P.idPoney = NEW.idPoney;

    -- Vérifier si le poney doit avoir un repos
    IF dernierCoursFin IS NOT NULL AND 
       TIMESTAMPDIFF(HOUR, dernierCoursFin, (SELECT dateDebut FROM SEANCE WHERE idSeance = NEW.idSeance)) < 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le poney doit avoir un repos d’au moins 1 heure après 2 heures de cours.';
    END IF;
END;
delimiter ;

delimiter |
CREATE OR REPLACE TRIGGER VerifReposPoney
BEFORE UPDATE ON PARTICIPER
FOR EACH ROW
BEGIN
    DECLARE dernierCoursFin DATETIME;
    DECLARE dureeTotale INT;

    SELECT MAX(S.dateFin), SUM(S.duree)
    INTO dernierCoursFin, dureeTotale
    FROM SEANCE S
    JOIN PARTICIPER P ON S.idSeance = P.idSeance
    WHERE P.idPoney = NEW.idPoney;
    IF dernierCoursFin IS NOT NULL AND 
       dureeTotale >= 2 AND 
       TIMESTAMPDIFF(HOUR, dernierCoursFin, (SELECT dateDebut FROM SEANCE WHERE idSeance = NEW.idSeance)) < 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le poney doit avoir un repos d’au moins 1 heure après 2 heures de cours.';
    END IF;
END;
delimiter ;

delimiter |
CREATE OR REPLACE TRIGGER VerifDureeCours
BEFORE INSERT ON SEANCE
FOR EACH ROW
BEGIN
  IF NEW.duree !=1 THEN
    RAISE(FAIL, 'La quantite commandee doit être inférieur ou égale à la quantité stocké');
  END IF;
END|
delimiter ;

delimiter |
CREATE OR REPLACE TRIGGER VerifDureeCours
BEFORE UPDATE ON SEANCE
FOR EACH ROW
BEGIN
  IF NEW.duree !=1 or NEW.duree !=2 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Un cours ne peut durer que 1 ou 2 heures.';
  END IF;
END|
delimiter ;


delimiter |
CREATE OR REPLACE TRIGGER VerifNbParticipants
BEFORE INSERT ON PARTICIPER
FOR EACH ROW
BEGIN
  DECLARE nbParticipants INT DEFAULT 0;
  DECLARE maxPersonnes INT DEFAULT 0;
  SELECT nbPersonneMax INTO maxPersonnes FROM SEANCE WHERE idSeance = NEW.idSeance;
  SELECT COUNT(*) INTO nbParticipants FROM PARTICIPER WHERE idSeance = NEW.idSeance;
  IF nbParticipants >= maxPersonnes THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Nombre maximum de participants atteint pour cette séance.';
  END IF;
END|
delimiter ;
