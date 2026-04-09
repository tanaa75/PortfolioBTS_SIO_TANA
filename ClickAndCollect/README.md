# Click & Collect — Boutique en ligne

Projet réalisé dans le cadre du **BTS SIO** (épreuve E6).  
Application web permettant aux clients de commander des articles en ligne et de les retirer en magasin grâce à un code de retrait.

---

## Prérequis

Avant de lancer le projet, il faut avoir installé :

- **Python 3.10+** — [Télécharger ici](https://www.python.org/downloads/)
- **pip** (inclus avec Python)
- **Git** (optionnel, pour cloner le dépôt)

## Installation

```bash
# 1. Cloner le projet
git clone https://github.com/tanaa75/Projet-Number-2.git
cd Projet-Number-2

# 2. Créer et activer l'environnement virtuel
python -m venv venv
venv\Scripts\activate

# 3. Installer les dépendances
pip install django Pillow
```

## Lancer le projet

```bash
# Appliquer les migrations (création des tables en base de données)
python manage.py migrate

# Démarrer le serveur de développement
python manage.py runserver
```

Le site sera accessible sur : **http://127.0.0.1:8000/boutique/**  
L'interface d'administration : **http://127.0.0.1:8000/admin/**

> Pour créer un compte administrateur : `python manage.py createsuperuser`

---

## Fonctionnalités

- Catalogue de produits avec gestion du stock
- Panier d'achat (stocké en session côté serveur)
- Inscription / Connexion client sécurisée
- Validation de commande avec génération d'un code de retrait unique
- Historique des commandes pour le client
- Interface d'administration pour gérer les produits, les commandes et les statuts de retrait

## Structure du projet

```
click_and_collect_project/
├── boutique/                  # Application principale
│   ├── models.py              # Modèles de données (Produit, Commande, LigneCommande)
│   ├── views.py               # Logique métier (panier, commande, catalogue)
│   ├── urls.py                # Routes de l'application
│   ├── admin.py               # Personnalisation de l'interface d'administration
│   └── templates/             # Pages HTML
├── click_and_collect_project/  # Configuration globale Django
│   ├── settings.py            # Paramètres du projet
│   └── urls.py                # Routes principales
├── media/                     # Images uploadées (produits)
├── db.sqlite3                 # Base de données SQLite
└── manage.py                  # Point d'entrée Django
```

## Technologies utilisées

| Outil       | Rôle                            |
|-------------|----------------------------------|
| Python      | Langage de programmation         |
| Django      | Framework web (architecture MVT) |
| SQLite      | Base de données                  |
| HTML / CSS  | Interface utilisateur            |
| Pillow      | Gestion des images produits      |
| Git / GitHub| Versionning du code              |
