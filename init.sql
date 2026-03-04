CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE enigmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero INT NOT NULL,
    titre VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    indice_1 TEXT,
    indice_2 TEXT,
    indice_3 TEXT,
    solution VARCHAR(255) NOT NULL,
    points INT DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE progression (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    enigme_id INT NOT NULL,
    resolu BOOLEAN DEFAULT FALSE,
    tentatives INT DEFAULT 0,
    date_resolution DATETIME DEFAULT NULL,
    temps_passe INT DEFAULT 0,
    indices_utilises INT DEFAULT 0,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (enigme_id) REFERENCES enigmes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_utilisateur_enigme (utilisateur_id, enigme_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO enigmes (numero, titre, description, indice_1, indice_2, indice_3, solution, points) VALUES

(1, 'Binaire',
 'Convertis ce message binaire en texte :\n01010011 01000101 01000011 01010010 01000101 01010100',
 'Chaque groupe de 8 bits représente une lettre',
 'Convertis chaque octet en décimal puis cherche la lettre ASCII',
 'S=83, E=69, C=67, R=82, E=69, T=84',
 'SECRET',
 20),

(2, 'César',
 'Décode ce message avec un décalage de 13 (ROT13) :\nCYNAGR',
 'ROT13 = chaque lettre est décalée de 13 positions dans l''alphabet',
 'C→P, Y→L, N→A, A→N, G→T, R→E',
 'CYNAGR → PLANTE',
 'PLANTE',
 40),

(3, 'Hexadécimal',
 'Convertis ce message hexadécimal en texte :\n4841 434B 4552',
 'Chaque paire de chiffres hexadécimaux = 1 lettre ASCII',
 '48=H, 41=A, 43=C, 4B=K, 45=E, 52=R',
 'H-A-C-K-E-R',
 'HACKER',
 60),

(4, 'Morse',
 'Décode ce message en morse :\n... .... .- -.. --- .--',
 'Chaque groupe séparé par un espace = 1 lettre',
 '... = S, .... = H, .- = A, -.. = D, --- = O, .-- = W',
 'S-H-A-D-O-W',
 'SHADOW',
 80),

(5, 'Acrostiche',
 'Prends la 2ème lettre de chaque mot :\nAMOUR NATURE ATOME TRISTE RIVIERE AXONE',
 'Regarde uniquement la 2ème lettre de chaque mot',
 'AMOUR→M, NATURE→A, ATOME→T, TRISTE→R, RIVIERE→I, AXONE→X',
 'M-A-T-R-I-X',
 'MATRIX',
 100);