<div align="center">

# 🌟 Aujourd'hui vers Demain

### Plateforme Web de Gestion Associative

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

*Application web complète développée pour l'association "Aujourd'hui vers Demain" de Noisy-le-Sec*

[📖 Documentation](#-fonctionnalités) • [🚀 Installation](#-installation-rapide) • [📸 Captures](#-captures-décran) • [👥 Équipe](#-équipe)

---

![Aperçu de l'application](docs/screenshots/hero_preview.jpg)

</div>

## 📋 À Propos

> **Projet de stage** réalisé dans le cadre de notre formation en développement web.

L'association **Aujourd'hui vers Demain** accompagne les habitants du quartier dans leur quotidien : aide aux devoirs, événements de quartier, bénévolat... Ce projet vise à **digitaliser** leurs activités grâce à une plateforme moderne et intuitive.

### 🎯 Objectifs du Projet

| Objectif | Description |
|----------|-------------|
| 🗄️ **Base de données** | Conception et modélisation d'une BDD relationnelle complète |
| 💻 **Développement Full-Stack** | Interface utilisateur moderne + logique serveur robuste |
| 🔐 **Back-Office sécurisé** | Espace d'administration complet pour l'association |
| 📱 **Responsive Design** | Compatible mobile, tablette et desktop |
| 📊 **Exports professionnels** | Export Excel, CSV et génération de reçus PDF |

---

## ✨ Fonctionnalités

### 🌐 Site Public (Front-Office)

<table>
<tr>
<td width="50%">

**🏠 Page d'Accueil**
- Design moderne "One Page"
- Animations fluides (AOS Library)
- Mode Sombre / Clair
- Section héro dynamique
- Statistiques animées

</td>
<td width="50%">

**📅 Gestion des Événements**
- Affichage des événements à venir
- Moteur de recherche intégré
- Pagination automatique
- Cartes avec images et détails

</td>
</tr>
<tr>
<td>

**📝 Inscriptions Aide aux Devoirs**
- Formulaire complet (nom, prénom, classe, adresse, téléphone, email)
- Pré-remplissage automatique pour les membres
- Validation des données en temps réel
- Confirmation visuelle après inscription

</td>
<td>

**🖼️ Galerie Photos**
- Affichage dynamique par catégories
- Filtres et tri par date
- Effet Lightbox au clic
- Photos événements + galerie

</td>
</tr>
<tr>
<td>

**❤️ Bénévolat**
- Formulaire de candidature complet
- Upload de CV (PDF, Word, Images)
- Champs disponibilités et compétences
- Réservé aux membres connectés

</td>
<td>

**📞 Contact**
- Formulaire de contact sécurisé
- Protection anti-spam
- Informations de l'association
- Carte interactive

</td>
</tr>
</table>

### 🔧 Espace Administrateur (Back-Office)

| Fonctionnalité | Description |
|----------------|-------------|
| 🔐 **Connexion sécurisée** | Authentification avec hachage bcrypt + protection brute force |
| 📊 **Dashboard** | Vue d'ensemble des événements avec statistiques |
| ➕ **CRUD Événements** | Créer, modifier, supprimer avec upload d'images |
| 🖼️ **Gestion Galerie** | Ajouter/supprimer des photos par catégorie |
| 📬 **Messagerie** | Centralisation des demandes (contact, inscriptions, bénévolat) |
| 📋 **Gestion Inscriptions** | Tableau des inscrits à l'aide aux devoirs |
| ✏️ **Modification Inscriptions** | Éditer les informations des enfants inscrits |
| 📥 **Export CSV/Excel** | Téléchargement des inscriptions avec mise en forme professionnelle |
| 📄 **Génération PDF** | Reçus d'inscription personnalisés avec logo |
| 🛡️ **Sécurité** | Logs de connexion et gestion des sessions |

### 📊 Nouvelles Fonctionnalités Pro

<table>
<tr>
<td width="33%" align="center">

**📥 Export Excel**

![Excel](https://img.shields.io/badge/PhpSpreadsheet-5.4-green?style=flat-square)

Export formaté avec :
- En-têtes colorés
- Colonnes auto-ajustées
- Alternance de couleurs

</td>
<td width="33%" align="center">

**📄 Génération PDF**

![TCPDF](https://img.shields.io/badge/TCPDF-6.10-red?style=flat-square)

Reçus professionnels avec :
- Logo de l'association
- Informations complètes
- Rappel des horaires

</td>
<td width="33%" align="center">

**✏️ Gestion Complète**

![CRUD](https://img.shields.io/badge/CRUD-Complet-blue?style=flat-square)

Actions disponibles :
- Modifier les inscriptions
- Envoyer des emails
- Supprimer les entrées

</td>
</tr>
</table>

### 🛡️ Sécurité Implémentée

- ✅ Protection CSRF sur tous les formulaires
- ✅ Hachage des mots de passe (`password_hash` bcrypt)
- ✅ Requêtes préparées (PDO) contre les injections SQL
- ✅ Validation et échappement des données (`htmlspecialchars`)
- ✅ Protection des uploads (types et tailles de fichiers)
- ✅ Sessions sécurisées avec timeout
- ✅ Limitation des tentatives de connexion
- ✅ Protection des dossiers via `.htaccess`

---

## 🛠️ Stack Technique

<div align="center">

| Catégorie | Technologies |
|-----------|--------------|
| **Back-End** | ![PHP](https://img.shields.io/badge/PHP_8-777BB4?style=flat-square&logo=php&logoColor=white) |
| **Base de Données** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) |
| **Front-End** | ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) |
| **Framework CSS** | ![Bootstrap](https://img.shields.io/badge/Bootstrap_5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white) |
| **Icônes** | ![Bootstrap Icons](https://img.shields.io/badge/Bootstrap_Icons-7952B3?style=flat-square&logo=bootstrap&logoColor=white) |
| **Animations** | ![AOS](https://img.shields.io/badge/AOS-Animate_On_Scroll-blue?style=flat-square) |
| **PDF** | ![TCPDF](https://img.shields.io/badge/TCPDF-6.10.1-red?style=flat-square) |
| **Excel** | ![PhpSpreadsheet](https://img.shields.io/badge/PhpSpreadsheet-5.4.0-green?style=flat-square) |
| **Outils** | ![VS Code](https://img.shields.io/badge/VS_Code-007ACC?style=flat-square&logo=visualstudiocode&logoColor=white) ![Laragon](https://img.shields.io/badge/Laragon-0E83CD?style=flat-square&logo=laragon&logoColor=white) ![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white) ![Composer](https://img.shields.io/badge/Composer-885630?style=flat-square&logo=composer&logoColor=white) |

</div>

---

## 🚀 Installation Rapide

### Prérequis

- PHP 8.0 ou supérieur
- MySQL 5.7 ou supérieur
- Composer (pour les dépendances)
- Serveur local (Laragon, WAMP, XAMPP...)

### Étapes d'installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/tanaa75/Aujourdhui-vers-demain-stage.git

# 2. Accéder au dossier
cd Aujourdhui-vers-demain-stage

# 3. Installer les dépendances PHP
composer install
```

### Configuration de la base de données

1. **Créer la base de données** dans phpMyAdmin :
   ```sql
   CREATE DATABASE asso_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Importer les tables** : Exécutez le script `database/schema.sql`

3. **Configurer la connexion** dans `includes/db.php` :
   ```php
   $host = 'localhost';
   $dbname = 'asso_db';
   $username = 'root';
   $password = '';
   ```

### Accès à l'application

| Page | URL | Identifiants |
|------|-----|--------------|
| 🏠 Site public | `http://localhost/aujourdhui-vers-demain/` | - |
| 🔐 Connexion Admin | `http://localhost/aujourdhui-vers-demain/auth/login.php` | `admin` / `admin123` |
| 📊 Dashboard | `http://localhost/aujourdhui-vers-demain/admin/dashboard.php` | Connexion requise |
| 📋 Inscriptions | `http://localhost/aujourdhui-vers-demain/admin/inscriptions.php` | Connexion requise |

---

## 📁 Structure du Projet

```
aujourdhui-vers-demain/
│
├── 📁 admin/                    # 🔧 Back-Office Administration
│   ├── dashboard.php            # Gestion des événements
│   ├── galerie.php              # Gestion galerie photos
│   ├── messages.php             # Messagerie centralisée
│   ├── inscriptions.php         # Liste des inscriptions aide aux devoirs
│   ├── edit_inscription.php     # Modifier une inscription
│   ├── export_inscriptions.php  # Export CSV & Excel
│   └── generate_pdf.php         # Génération reçus PDF
│
├── 📁 auth/                     # 🔐 Authentification
│   ├── login.php                # Connexion admin
│   ├── inscription.php          # Inscription membre
│   ├── connexion.php            # Connexion membre
│   └── logout.php               # Déconnexion
│
├── 📁 pages/                    # 🌐 Pages Publiques
│   ├── actions.php              # Nos actions (aide aux devoirs)
│   ├── galerie.php              # Galerie photos dynamique
│   ├── benevolat.php            # Devenir bénévole
│   └── contact.php              # Formulaire de contact
│
├── 📁 includes/                 # ⚙️ Fichiers partagés
│   ├── db.php                   # Configuration BDD
│   ├── navbar.php               # Barre de navigation
│   ├── footer.php               # Pied de page
│   ├── security.php             # Fonctions de sécurité (CSRF)
│   └── config.php               # Configuration globale
│
├── 📁 assets/                   # 🎨 Ressources statiques
│   ├── css/
│   │   ├── index.css            # Styles page d'accueil
│   │   ├── admin.css            # Styles administration
│   │   └── mobile-responsive.css # Styles responsive
│   └── js/
│       ├── index.js             # Scripts page d'accueil
│       └── script_theme.js      # Gestion thème jour/nuit
│
├── 📁 uploads/                  # 📤 Fichiers uploadés
│   ├── events/                  # Images événements
│   ├── gallery/                 # Photos galerie
│   └── cv/                      # CV des bénévoles
│
├── 📁 vendor/                   # 📦 Dépendances Composer
│   ├── phpoffice/phpspreadsheet # Export Excel
│   └── tecnickcom/tcpdf         # Génération PDF
│
├── 📁 pages/legal/              # ⚖️ Pages légales
│   ├── mentions.php             # Mentions légales
│   └── confidentialite.php      # Politique de confidentialité
│
├── 📄 index.php                 # Page d'accueil principale
├── 📄 composer.json             # Dépendances PHP
└── 📄 README.md                 # Documentation
```

---

## 📸 Captures d'écran

<div align="center">

### 🏠 Page d'Accueil

| Mode Clair | Mode Sombre |
|------------|-------------|
| ![Accueil Light](docs/screenshots/home_light.jpg) | ![Accueil Dark](docs/screenshots/home_dark.jpg) |

### 🔧 Administration

| Dashboard | Inscriptions |
|-----------|--------------|
| ![Dashboard](docs/screenshots/admin_dashboard.jpg) | ![Inscriptions](docs/screenshots/admin_inscriptions.jpg) |

### 📊 Exports

| Export Excel | Reçu PDF |
|--------------|----------|
| ![Excel](docs/screenshots/export_excel.jpg) | ![PDF](docs/screenshots/receipt_pdf.jpg) |

</div>

---

## 🗄️ Schéma de la Base de Données

```mermaid
erDiagram
    UTILISATEURS ||--o{ MESSAGES : envoie
    MEMBRES ||--o{ MESSAGES : envoie
    
    UTILISATEURS {
        int id PK
        varchar email UK
        varchar mot_de_passe
        datetime date_ajout
    }
    
    MEMBRES {
        int id PK
        varchar nom
        varchar email UK
        varchar mot_de_passe
        datetime date_inscription
    }
    
    EVENEMENTS {
        int id PK
        varchar titre
        text description
        date date_evenement
        varchar lieu
        varchar image
        datetime created_at
    }
    
    PHOTOS {
        int id PK
        varchar titre
        text description
        varchar image
        varchar categorie
        datetime date_ajout
    }
    
    MESSAGES {
        int id PK
        varchar nom
        varchar email
        text message
        datetime date_envoi
        boolean lu
    }
```

---

## 📦 Dépendances

### Composer (PHP)

```json
{
    "require": {
        "phpoffice/phpspreadsheet": "^5.4",
        "tecnickcom/tcpdf": "^6.10"
    }
}
```

### CDN (Front-End)

| Librairie | Version | Usage |
|-----------|---------|-------|
| Bootstrap | 5.3.0 | Framework CSS |
| Bootstrap Icons | 1.11.0 | Icônes |
| AOS | 2.3.1 | Animations scroll |

---

## 👥 Équipe

<div align="center">

| Développeur | Rôle | Contact |
|-------------|------|---------|
| **CA TANAVONG** | Développeur Full-Stack | [![GitHub](https://img.shields.io/badge/GitHub-tanaa75-181717?style=flat-square&logo=github)](https://github.com/tanaa75) |
| **BEDJOU AYOUB** | Développeur Full-Stack | [![GitHub](https://img.shields.io/badge/GitHub-ayoub-181717?style=flat-square&logo=github)](https://github.com) |

</div>

---

## 📄 Licence

Ce projet a été réalisé dans le cadre d'un **stage de formation**.  
Tous droits réservés © 2026 - CA TANAVONG & BEDJOU AYOUB

---

<div align="center">

**⭐ Si ce projet vous a plu, n'hésitez pas à lui donner une étoile !**

[![Made with ❤️](https://img.shields.io/badge/Made%20with-❤️-red?style=for-the-badge)](https://github.com/tanaa75)
[![Maintained](https://img.shields.io/badge/Maintained-Yes-green?style=for-the-badge)](https://github.com/tanaa75)

</div>
