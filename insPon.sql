
INSERT INTO PERSONNE (idPersonne, prenom, nom, numTel, email, poids) VALUES 
(1, 'Alice', 'Martin', 612345678, 'alice@example.com', 65.5),
(2, 'Bob', 'Dupont', 613456789, 'bob@example.com', 70.0); 

INSERT INTO CLIENT (idCl, idPersonne, dateDeNaissance, niveau, cotisation) VALUES 
(1, 2, '1990-01-01', 'Débutant', 'Payée'),
(2, 2, '1990-01-01', 'Debutant', 'Payée');

INSERT INTO PONEY (idPoney, nomP, poidsMax) VALUES 
(1, 'Tonnerre', 80.0), 
(2, 'Éclair', 90.0);

INSERT INTO COURS (idCours, typeCours) VALUES 
(1, 'Particulier'), 
(2, 'Collectif');

INSERT INTO SEANCE (idSeance, dateDebut, dateFin, duree, particulier, nbPersonneMax, niveau, idMoniteur, idCours) VALUES
(1, '2024-12-18 09:00:00', '2024-11-18 10:00:00', 1, TRUE, 1, 'Débutant', 1, 1),  
(2, '2024-12-18 10:00:00', '2024-11-18 12:00:00', 2, FALSE, 10, 'Intermédiaire', 1, 2),
(3, '2024-12-18 12:30:00', '2024-11-18 12:30:00', 5, TRUE, 1, 'Débutant', 1, 1); 

INSERT INTO PARTICIPER (idSeance, idPoney) VALUES 
(1, 1),
(2, 1), 
(2, 2); 

INSERT INTO ASSISTER (idCl, idSeance, statutPayement, restePayement) VALUES 
(1, 1, 'Payé', 0),
(2, 1, 'Non Payé', 50);
