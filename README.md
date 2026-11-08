# GameHub

Een eenvoudig gamingforum. Bezoekers kunnen categorieën en onderwerpen bekijken. Na registratie kunnen gebruikers onderwerpen en reacties plaatsen. Alleen beheerders kunnen categorieën en bijdragen verwijderen of gebruikers beheren.

## Benodigd

- PHP 8.2 of hoger met MySQL- en SQLite-extensies
- Composer
- MySQL
- Node.js en npm

## Installeren op Windows

Maak eerst een MySQL-database met de naam `gamehub`. Voer daarna in de projectmap uit:

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
```

Pas zo nodig `DB_USERNAME` en `DB_PASSWORD` in `.env` aan. Genereer daarna de sleutel, zet de database klaar en bouw de frontend:

```powershell
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000` en registreer een account. E-mailverificatie staat uit, zodat je direct kunt inloggen.

## Beheerder instellen

Maak eerst een account aan. Voer daarna `php artisan tinker` uit en voer bij de Tinker-prompt het volgende in met het e-mailadres van dat account:

```php
$user = App\Models\User::where('email', 'jouw-email@example.com')->firstOrFail();
$user->is_admin = true;
$user->save();
```

Nieuwe accounts zijn standaard geen beheerder. Beheerders kunnen categorieën toevoegen, onderwerpen en reacties verwijderen en accounts beheren.

## Wachtwoord vergeten

Voor lokaal gebruik schrijft GameHub de resetlink naar `storage/logs/laravel.log`. Open die link om een nieuw wachtwoord in te stellen.

## Tests

```powershell
php artisan test
```
