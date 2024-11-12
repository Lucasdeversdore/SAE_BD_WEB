CREATE TABLE assister (
  PRIMARY KEY (idCl, idSeance),
  idCl           VARCHAR(42) NOT NULL,
  idSeance       VARCHAR(42) NOT NULL,
  statutPayement VARCHAR(42),
  restePayement  VARCHAR(42)
);

CREATE TABLE client (
  PRIMARY KEY (idCl),
  idCl            VARCHAR(42) NOT NULL,
  dateDeNaissance VARCHAR(42),
  niveau          VARCHAR(42),
  cotisation      VARCHAR(42)
);

CREATE TABLE cours (
  PRIMARY KEY (idCours),
  idCours   VARCHAR(42) NOT NULL,
  typeCours VARCHAR(42)
);

CREATE TABLE historique (
  PRIMARY KEY (idSuivi),
  idSuivi          VARCHAR(42) NOT NULL,
  dateAchat        VARCHAR(42),
  descriptionAchat VARCHAR(42)
);

CREATE TABLE participer (
  PRIMARY KEY (idSeance, idPoney),
  idSeance VARCHAR(42) NOT NULL,
  idPoney  VARCHAR(42) NOT NULL
);

CREATE TABLE personne (
  PRIMARY KEY (idPersonne),
  idPersonne VARCHAR(42) NOT NULL,
  prenom     VARCHAR(42),
  nom        VARCHAR(42),
  numTel     VARCHAR(42),
  email      VARCHAR(42),
  poids      VARCHAR(42)
);

CREATE TABLE poney (
  PRIMARY KEY (idPoney),
  idPoney  VARCHAR(42) NOT NULL,
  nomP     VARCHAR(42),
  poidsMax VARCHAR(42)
);

CREATE TABLE rajouter (
  PRIMARY KEY (idCl, idSuivi),
  idCl    VARCHAR(42) NOT NULL,
  idSuivi VARCHAR(42) NOT NULL
);

CREATE TABLE seance (
  PRIMARY KEY (idSeance),
  idSeance      VARCHAR(42) NOT NULL,
  dateDebut     VARCHAR(42),
  dateFin       VARCHAR(42),
  duree         VARCHAR(42),
  particulier   VARCHAR(42),
  nbPersonneMax VARCHAR(42),
  niveau        VARCHAR(42),
  idMoniteur    VARCHAR(42) NOT NULL,
  idCours       VARCHAR(42) NOT NULL
);

ALTER TABLE assister ADD FOREIGN KEY (idSeance) REFERENCES seance (idSeance);
ALTER TABLE assister ADD FOREIGN KEY (idCl) REFERENCES client (idCl);

ALTER TABLE participer ADD FOREIGN KEY (idPoney) REFERENCES poney (idPoney);
ALTER TABLE participer ADD FOREIGN KEY (idSeance) REFERENCES seance (idSeance);

ALTER TABLE rajouter ADD FOREIGN KEY (idSuivi) REFERENCES historique (idSuivi);
ALTER TABLE rajouter ADD FOREIGN KEY (idCl) REFERENCES client (idCl);

ALTER TABLE seance ADD FOREIGN KEY (idCours) REFERENCES cours (idCours);