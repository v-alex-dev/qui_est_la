# Qui est là ?

Application de gestion des visiteurs pour entreprise avec interface d'administration complète.

## Fonctionnalités

### API de gestion des visiteurs
- Enregistrement des entrées/sorties de visiteurs
- Recherche de visiteurs par email
- Gestion des badges visiteurs
- Support des formations et du personnel

### Interface d'administration
- Dashboard avec statistiques en temps réel
- Gestion des visiteurs et historique des visites
- Gestion du personnel et des formations
- Interface web responsive avec authentification sécurisée

## Prérequis
- PHP ^8.2
- Composer (gestionnaire de dépendances PHP)
- MySQL (ou MariaDB) pour la base de données
- Node.js (recommandé : v18+) et npm (pour la compilation des assets front)
- Extensions PHP courantes :
  - OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfoà ?

Application de gestion des visiteurs pour entreprise.

## Requirement
+PHP ^8.2
+Composer (gestionnaire de dépendances PHP)
+MySQL (ou MariaDB) pour la base de données
+Node.js (recommandé : v18+) et npm (pour la compilation des assets front)
+Extensions PHP courantes :
+OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo

## Installation

```bash
git clone https://github.com/v-alex-dev/qui_est_la
cd qui_est_la
composer install
cp .env.example .env
# Configurer .env (DB, etc.)
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
```

> **Note :** L'option `--seed` lors de la migration crée automatiquement un utilisateur administrateur pour tester l'application.

## Lancer le serveur

```bash
php artisan serve
```

## Structure du projet

- `app/Models` : Modèles Eloquent (User, Visitor, Visit, StaffMember, Training)
- `app/Http/Controllers/Api` : Contrôleurs API pour la gestion des visiteurs
- `app/Http/Controllers/Web` : Contrôleurs web pour l'administration
- `app/Http/Middleware` : Middlewares personnalisés (IsAdmin)
- `resources/views` : Vues Blade pour l'interface web
- `database/seeders` : Seeders pour les données de test

## API Endpoints

### Gestion des visiteurs
- `POST /api/v1/enter` : Enregistrer l'entrée d'un visiteur
- `POST /api/v1/exit` : Enregistrer la sortie d'un visiteur  
- `POST /api/v1/return` : Gestion des visiteurs qui reviennent
- `GET /api/v1/visitor/search?email=...` : Rechercher un visiteur par email
- `GET /api/v1/visitor/badge?badge_id=...` : Récupérer un visiteur par ID de badge

### Données de référence
- `GET /api/v1/staff-members` : Liste du personnel
- `GET /api/v1/trainings/today` : Formations du jour

## Interface d'administration

### Identifiants de test
- **Email :** `admin@example.com`
- **Mot de passe :** `admin123`

### Pages disponibles
- `/admin/dashboard` : Dashboard principal avec statistiques
- `/admin/history` : Historique des visites
- `/admin/manage-data` : Gestion du personnel et des formations

### Fonctionnalités admin
- Visualisation en temps réel des visiteurs présents
- Gestion complète du personnel de l'entreprise
- Gestion des formations et événements
- Historique détaillé des visites avec filtres
- Interface responsive adaptée aux tablettes et mobiles

## Technologies utilisées

- **Backend :** Laravel 11, PHP 8.2+
- **Frontend :** Blade, TailwindCSS, Alpine.js
- **Base de données :** SQLite (développement), MySQL/MariaDB (production)
- **Build :** Vite, npm
- **Tests :** Pest PHP

## Liens

- **🌐 Application Frontend :** [https://pontoise-qui-est-la.netlify.app/](https://pontoise-qui-est-la.netlify.app/)
- **⚙️ API Backend :** [https://qui-est-la-main-i6aqmb.laravel.cloud/](https://qui-est-la-main-i6aqmb.laravel.cloud/)

## Auteurs

- Vens Alexandre