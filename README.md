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
| [tomatophp/filament-cms](https://github.com/tomatophp/filament-cms) | 5.x |
| [tomatophp/filament-menus](https://github.com/tomatophp/filament-menus) | 5.x |
| [tomatophp/filament-media-manager](https://github.com/tomatophp/filament-media-manager) | 5.x |
| [tomatophp/filament-types](https://github.com/tomatophp/filament-types) | 5.x |
| [tomatophp/filament-accounts](https://github.com/tomatophp/filament-accounts) | 5.x |
| [tomatophp/filament-meta](https://github.com/tomatophp/filament-meta) | 5.x |
| [tomatophp/filament-employees](https://github.com/tomatophp/filament-employees) | 5.x |
| [tomatophp/filament-locations](https://github.com/tomatophp/filament-locations) | 5.x |
| [tomatophp/filament-invoices](https://github.com/tomatophp/filament-invoices) | 5.x |
| [tomatophp/filament-subscriptions](https://github.com/tomatophp/filament-subscriptions) | 5.x |
| [tomatophp/filament-form-builder](https://github.com/tomatophp/filament-form-builder) | 5.x |
| [tomatophp/filament-issues](https://github.com/tomatophp/filament-issues) | 5.x |
| [tomatophp/filament-docs](https://github.com/tomatophp/filament-docs) | 5.x |
| [tomatophp/filament-bookmarks-menu](https://github.com/tomatophp/filament-bookmarks-menu) | 5.x |
| [tomatophp/filament-notes](https://github.com/tomatophp/filament-notes) | 5.x |
| [tomatophp/filament-ecommerce](https://github.com/tomatophp/filament-ecommerce) | 5.x |
| [tomatophp/filament-pos](https://github.com/tomatophp/filament-pos) | 5.x |
| [tomatophp/filament-plugins](https://github.com/tomatophp/filament-plugins) | 5.x |

More plugins are added here as each one is released for Filament 5. Plugins that cannot be made safe
for a public demo are left out: `filament-payments` (its gateway settings page stores live API keys
and checkout calls the real providers), and the command and file tools `filament-artisan` and
`filament-browser`. `filament-blog` needs a second panel and a front-end build, and `filament-workflows`
is not published yet; both are still to come.

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
- command and file tools such as the artisan runner and file browser are not installed on the public demo, and the
  developer gate uses a private `DEVELOPER_GATE_PASSWORD`
- pages that store credentials (mail, Discord, invoice settings) redirect to the dashboard, and actions that send mail
  or call GitHub (invoice emails, issue refresh and clean) are hidden
- features that write files or generate code are off: the plugin manager is read only
  (`allowCreate(false)`, `allowImport(false)`, `allowToggle(false)`, `allowDestroy(false)`,
  `allowGenerator(false)`) and `/admin/tables`, its table builder, is locked
- imports and exports are off everywhere (CMS, accounts, ecommerce orders), notes are local only
  (no share links, notifications or per-user access)

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
