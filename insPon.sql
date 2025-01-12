INSERT INTO PERSONNE (idPersonne, prenom, nom, numTel, email, poids, mdp)
VALUES (1, 'Jean', 'Dupont', 123456789, 'jean.dupont@example.com', 75.0, 'password123'),
       (2, 'Marie', 'Martin', 987654321, 'marie.martin@example.com', 60.0, 'password456'),
       (3, 'Lucas', 'Devers-Dore', 12145456, 'lucasdeversdore@gmail.com', 80, '$2y$12$rmmPrABovYtGKEuBlFRkIurHH7zUqAhFj2g9zQGT4nSzcBEPKoPAq'),
       (4, 'test', 'test-test', 121454546, 'test@test', 1, '$2y$12$rmmPrABovYtGKEuBlFRkIurHH7zUqAhFj2g9zQGT4nSzcBEPKoPAq');

INSERT INTO CLIENT (idCl, idPersonne, dateDeNaissance, niveau, cotisation)
VALUES (1, 1, '1990-06-25', 'Débutant', 'Annuel'),
        (2, 3, '2004-11-18', 'Débutant', 'Annuel');


INSERT INTO MONITEUR (idMoniteur, idPersonne, dateDeNaissance)
VALUES (1, 2, '1988-03-20');  


INSERT INTO COURS (idCours, typeCours)
VALUES (1, 'Dressage'), 
       (2, 'Balade');


INSERT INTO PONEY (idPoney, nomP, poidsMax, imagePoney)
VALUES (1, 'Poney1', 500, 'poney1.jpg'), 
       (2, 'Poney2', 450, 'poney2.jpg'),
        (3, 'Poney3', 40, 'poney3.jpg'),
        (4, 'Poney4', 80, 'poney4.jpg'),
        (5, 'Poney5', 57, 'poney5.jpg'),
        (6, 'Poney6', 70, 'poney6.jpg'),
        (7, 'Poney7', 40, 'poney7.jpg'),
        (8, 'Poney8', 36, 'poney8.jpg'),
        (9, 'Poney9', 90, 'poney9.jpg'),
        (10, 'Poney10', 92, 'poney10.jpg');

INSERT INTO SEANCE (idSeance, dateDebut, dateFin, duree, particulier, nbPersonneMax, niveau, idMoniteur, idCours)
VALUES 
    (1, '2025-02-01 09:00:00', '2025-02-01 10:00:00', 60, 0, 10, 'Débutant', 1, 1),  -- Marie Martin en tant que monitrice
    (2, '2025-02-03 10:00:00', '2025-02-03 12:00:00', 120, 1, 5, 'Avancé', 1, 2),  -- Marie Martin en tant que monitrice
    (9, '2025-02-03 12:20:00', '2025-02-03 14:00:00', 120, 0, 10, 'Débutant', 1, 1),  -- Séance de dressage
    (3, '2025-02-05 08:00:00', '2025-02-05 09:00:00', 60, 0, 10, 'Débutant', 1, 1),  -- Séance de dressage
    (4, '2025-02-06 15:00:00', '2025-02-06 16:30:00', 90, 1, 5, 'Avancé', 1, 2),    -- Séance de balade
    (5, '2025-02-07 10:00:00', '2025-02-07 11:00:00', 60, 0, 8, 'Intermédiaire', 1, 1),  -- Séance de dressage
    (6, '2025-02-08 14:00:00', '2025-02-08 15:15:00', 75, 1, 6, 'Avancé', 1, 2),    -- Séance de balade
    (7, '2025-02-09 09:00:00', '2025-02-09 10:00:00', 60, 0, 10, 'Débutant', 1, 1),  -- Séance de dressage
    (8, '2025-02-10 16:00:00', '2025-02-10 17:30:00', 90, 1, 5, 'Avancé', 1, 2);    -- Séance de balade

INSERT INTO PARTICIPER (idSeance, idPoney, idCl)
VALUES (1, 1, 1); 

