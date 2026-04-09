# 🧠 Jeu de Mémoire

> Projet réalisé dans le cadre du **BTS SIO** (option SLAM) — Épreuve E4.

![Python](https://img.shields.io/badge/Python-3.x-blue?style=for-the-badge&logo=python)
![Tkinter](https://img.shields.io/badge/GUI-Tkinter-green?style=for-the-badge)
![SQLite](https://img.shields.io/badge/Database-SQLite-orange?style=for-the-badge)

---

## À propos

Application de bureau codée en Python avec Tkinter. C'est un jeu de Memory classique où il faut retrouver les paires de cartes cachées.

Le projet utilise une approche orientée objet avec plusieurs classes séparées, et une base SQLite pour sauvegarder les scores.

## Fonctionnalités

- **4 niveaux de difficulté** : Facile (3×4), Normal (4×4), Difficile (5×6), Expert (6×6)
- **3 thèmes visuels** : Chiffres, Fruits 🍎, Sports ⚽
- **Sauvegarde des scores** en base SQLite avec pseudo, temps et nombre de coups
- **Hall of Fame** : classement des meilleurs scores avec filtres par niveau et thème
- **Sons** : gestionnaire audio avec sons de clic, paire trouvée, erreur et victoire
- **Chronomètre** en temps réel pendant la partie
- **Interface scrollable** pour que tout soit visible quel que soit le niveau
- **Mode Aide Étendu** : Révélation d'un nombre choisi de cartes pour s'aider.
- **Mode Triche (Secret)** 🤫 : Un bouton ultra discret permet de sauvegarder secrètement la grille et de la recharger à l'identique pour faire croire qu'on est bluffant de chance !

## Structure du projet

```
├── main.py          → point d'entrée, lance le menu
├── menu.py          → menu principal (choix niveau, thème, accès records)
├── game.py          → logique du jeu (interface, clics, vérification des paires)
├── board.py         → gestion du plateau (grille de cartes)
├── card.py          → classe Carte (valeur, état visible/caché)
├── database.py      → connexion SQLite et requêtes (ajout/lecture des scores)
└── sounds.py        → gestion des sons (bips système / winsound sur Windows)
```

## Installation

### Prérequis

- Python 3.10 ou plus récent
- Aucune dépendance externe (uniquement des bibliothèques intégrées à Python)

### Lancement

```bash
# cloner le projet
git clone https://github.com/tanaa75/Projet-Number-1.git
cd Projet-Number-1

# lancer le jeu
python main.py
```

## Technologies utilisées

| Technologie | Utilisation |
|---|---|
| **Python** | Langage principal |
| **Tkinter** | Interface graphique (fenêtres, boutons, grille) |
| **SQLite** | Stockage des scores en local |
| **winsound** | Sons sur Windows |
| **threading** | Sons joués en parallèle pour pas bloquer le jeu |

## Aperçu

Le jeu se lance sur un **menu principal** où on choisit le niveau et le thème, puis on accède à la grille de cartes. Il faut retrouver toutes les paires le plus vite possible et en le moins de coups. À la fin, on entre son pseudo et le score est enregistré en base.

---

*Projet BTS SIO SLAM — 2025*
