# TomatoPHP Demo

The live demo for every [TomatoPHP](https://tomatophp.com) Filament plugin, running on Laravel 13 and Filament 5
with the [TomatoPHP theme](https://github.com/tomatophp/filament-tomatophp-theme).

- Demo: https://demo.tomatophp.com
- Account: `demo@tomatophp.com` / `demo1234` (prefilled on the login page)
- Demo data resets every hour

The previous version of this repository is kept on the `backup` branch.

## What's inside

| Plugin | Version |
|--------|---------|
| [tomatophp/filament-tomatophp-theme](https://github.com/tomatophp/filament-tomatophp-theme) | 5.x |
| [tomatophp/filament-users](https://github.com/tomatophp/filament-users) | 5.x |
| [tomatophp/filament-settings-hub](https://github.com/tomatophp/filament-settings-hub) | 5.x |
| [tomatophp/filament-developer-gate](https://github.com/tomatophp/filament-developer-gate) | 5.x |
| [tomatophp/filament-icons](https://github.com/tomatophp/filament-icons) | 5.x |
| [tomatophp/filament-alerts](https://github.com/tomatophp/filament-alerts) | 5.x |
| [tomatophp/filament-discord-driver](https://github.com/tomatophp/filament-discord-driver) | 5.x |
| [tomatophp/filament-translation-component](https://github.com/tomatophp/filament-translation-component) | 5.x |
| [tomatophp/filament-translations](https://github.com/tomatophp/filament-translations) | 5.x |
| [tomatophp/filament-translations-google](https://github.com/tomatophp/filament-translations-google) | 5.x |
| [tomatophp/filament-translations-gpt](https://github.com/tomatophp/filament-translations-gpt) | 5.x |
| [tomatophp/filament-language-switcher](https://github.com/tomatophp/filament-language-switcher) | 5.x |
| [tomatophp/filament-wallet](https://github.com/tomatophp/filament-wallet) | 5.x |

More plugins are added here as each one is released for Filament 5.

## Run it locally

```bash
git clone https://github.com/tomatophp/tomatophp.com.git
cd tomatophp.com
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan filament:assets
php artisan serve
```

Open http://localhost:8000/admin and sign in with the demo account.

## Demo mode

Set `DEMO_MODE=true` in `.env` for a public deployment:

- the login page is prefilled with `DEMO_EMAIL` / `DEMO_PASSWORD`
- the demo and admin (`DEMO_ADMIN_EMAIL`) accounts cannot be edited, deleted, have their password changed or be impersonated from the panel
- `php artisan schedule:work` (or a cron running `schedule:run`) rebuilds the database from the seeders every hour
- command and file tools such as the artisan runner and file browser stay behind the developer gate

Every plugin added to the demo ships a seeder in `database/seeders` so a fresh `migrate --seed` shows it with data.

## Developing the plugins

The plugins are developed side by side in `packages/` (ignored by git) and resolved through a Composer path repository,
so the demo always runs the working copy. Without a `packages/` folder, run `composer update "tomatophp/*"` once so Composer
resolves the released versions from Packagist instead of the local path entries in `composer.lock`.

```bash
php artisan test
```

runs the panel smoke test (every page of every panel renders for a signed-in user), the demo-mode tests and the plugin integration tests.

## License

The MIT License (MIT).
