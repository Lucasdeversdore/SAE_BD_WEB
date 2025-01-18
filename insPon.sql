INSERT INTO PERSONNE (idPersonne, prenom, nom, numTel, email, poids, mdp, est_admin, est_moniteur)
VALUES   (10, 'moniteur', 'moniteur-moniteur', 121454554426, 'mon@mon', 1, '$2y$12$rmmPrABovYtGKEuBlFRkIurHH7zUqAhFj2g9zQGT4nSzcBEPKoPAq', FALSE, TRUE),
        (3, 'Lucas', 'Devers-Dore', 12145456, 'lucasdeversdore@gmail.com', 80, '$2y$12$rmmPrABovYtGKEuBlFRkIurHH7zUqAhFj2g9zQGT4nSzcBEPKoPAq', FALSE, FALSE),
       (4, 'test', 'test-test', 121454546, 'test@test', 1, '$2y$12$rmmPrABovYtGKEuBlFRkIurHH7zUqAhFj2g9zQGT4nSzcBEPKoPAq', FALSE, FALSE),
       (5, 'admin', 'admin-admin', 12145454426, 'admin@admin', 1, '$2y$12$rmmPrABovYtGKEuBlFRkIurHH7zUqAhFj2g9zQGT4nSzcBEPKoPAq', TRUE, FALSE);

INSERT INTO CLIENT (idCl, idPersonne, dateDeNaissance, niveau, cotisation)
VALUES (1, 1, '1990-06-25', 'Débutant', 'Annuel'),
        (2, 3, '2004-11-18', 'Débutant', 'Annuel');


INSERT INTO MONITEUR (idMoniteur, idPersonne, dateDeNaissance)
VALUES (1, 10, '1988-03-20');
        


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
    (1, '2025-02-01 09:00:00', '2025-02-01 10:00:00', 1, FALSE, 10, 'Débutant', 1, 1),  
    (2, '2025-02-03 10:00:00', '2025-02-03 12:00:00', 2, TRUE, 5, 'Avancé', 1, 2), 
    (3, '2025-02-05 08:00:00', '2025-02-05 09:00:00', 1, FALSE, 10, 'Débutant', 1, 1),  
    (4, '2025-02-06 15:00:00', '2025-02-06 17:00:00', 2, TRUE, 5, 'Avancé', 1, 2),   
    (5, '2025-02-07 10:00:00', '2025-02-07 11:00:00', 1, FALSE, 8, 'Intermédiaire', 1, 1),
    (6, '2025-02-08 14:00:00', '2025-02-08 15:00:00', 1, TRUE, 6, 'Avancé', 1, 2),    
    (7, '2025-02-09 09:00:00', '2025-02-09 10:00:00', 1, FALSE, 10, 'Débutant', 1, 1), 
    (8, '2025-02-10 16:00:00', '2025-02-10 18:00:00', 2, TRUE, 5, 'Avancé', 1, 2),  
    (10, '2025-01-13 09:00:00', '2025-01-13 09:00:00', 1, TRUE, 1, 'Débutant', 1, 1),  
    (11, '2025-01-14 10:00:00', '2025-01-14 12:00:00', 2, FALSE, 10, 'Intermédiaire', 1, 2),
    (12, '2025-01-15 12:30:00', '2025-01-15 13:30:00', 1, TRUE, 1, 'Débutant', 1, 1);



INSERT INTO PARTICIPER (idSeance, idPoney, idCl)
VALUES (1, 1, 1); 

