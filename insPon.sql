INSERT INTO PERSONNE (idPersonne, prenom, nom, numTel, email, mdp, poids) 
VALUES (1, 'Alice', 'Dupont', '0612345678', 'alice.dupont@example.com', 'StrongP@ss1', 60.5);

INSERT INTO PERSONNE (idPersonne, prenom, nom, numTel, email, mdp, poids) 
VALUES (2, 'Bob', 'Martin', '0612345679', 'bob.martin@example.com', 'weakpass1!', 72.3);

INSERT INTO SEANCE (idSeance, dateDebut, dateFin, duree, particulier, nbPersonneMax, niveau, idMoniteur, idCours)
VALUES (1, '2024-11-22 14:00:00', '2024-11-22 16:00:00', 2, FALSE, 5, 'Débutant', 1, 1);

INSERT INTO SEANCE (idSeance, dateDebut, dateFin, duree, particulier, nbPersonneMax, niveau, idMoniteur, idCours)
VALUES (2, '2024-11-22 17:00:00', '2024-11-22 20:00:00', 3, FALSE, 5, 'Intermédiaire', 1, 1);

INSERT INTO PONEY (idPoney, nomP, poidsMax) VALUES (1, 'Poney1', 100);

INSERT INTO SEANCE (idSeance, dateDebut, dateFin, duree, particulier, nbPersonneMax, niveau, idMoniteur, idCours)
VALUES (3, '2024-11-22 10:00:00', '2024-11-22 11:00:00', 1, FALSE, 1, 'Débutant', 1, 1);

INSERT INTO PARTICIPER (idSeance, idPoney) VALUES (3, 1);

INSERT INTO PARTICIPER (idSeance, idPoney) VALUES (3, 2);

INSERT INTO SEANCE (idSeance, dateDebut, dateFin, duree, particulier, nbPersonneMax, niveau, idMoniteur, idCours)
VALUES (4, '2024-11-22 11:00:00', '2024-11-22 12:00:00', 1, FALSE, 5, 'Débutant', 1, 1);

INSERT INTO PARTICIPER (idSeance, idPoney) VALUES (4, 1);
