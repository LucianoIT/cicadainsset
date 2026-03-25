# ⬡ CICADA 3302

Plateforme web de défis cryptographiques progressifs, les joueurs doivent résoudre 5 énigmes (binaire, ROT13, hexadécimal, morse, acrostiche) pour accumuler des points et grimper dans le classement.

---

## Installation

```bash
git clone https://github.com/LucianoIT/cicadainsset.git
cd cicadainsset
docker compose up -d
```

L'application est disponible sur **http://localhost:8080**

---

---

## Utilisation

1. Créer un compte sur la page d'inscription
2. Se connecter
3. Résoudre les énigmes dans l'ordre depuis le tableau de bord
4. Consulter le classement

Les énigmes se débloquent une par une — il faut résoudre la précédente pour accéder à la suivante.

---

## Base de données

3 tables :

- **utilisateurs** — id, pseudo, email, mot_de_passe, date_inscription, derniere_connexion
- **enigmes** — id, numero, titre, description, indice_1/2/3, solution, points
- **progression** — id, utilisateur_id, enigme_id, resolu, tentatives, date_resolution, indices_utilises

La base est initialisée automatiquement via `init.sql` au premier démarrage.

---

## Services Docker

| Service | URL |
|---------|-----|
| Application | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
