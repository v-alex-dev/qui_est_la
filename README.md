# Qui est là ?

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
php artisan migrate
npm install && npm run build
```

## Lancer le serveur

```bash
php artisan serve
```

## Structure

- `app/Models` : Modèles Eloquent
- `app/Http/Controllers/Api` : Contrôleurs API
- `app/Http/Controllers/Admin` : Contrôleurs Admin SSR
- `resources/views` : Vues Blade

## API

- `POST /api/visitors/enter`
- `POST /api/visitors/exit`
- `GET /api/visitors/search?email=...`

## Admin

- `/admin/dashboard` : Acceuil dashboard

## Auteurs

- Vens Alexandre