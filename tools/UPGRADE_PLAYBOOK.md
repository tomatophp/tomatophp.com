# tomatophp → Filament v5 upgrade playbook

Host app: `E:\Sites\tomatophp` (Laravel 13.31, Filament 5.8). Every package lives in
`E:\Sites\tomatophp\packages\<repo>` as its own git clone of `github.com/tomatophp/<repo>`,
and is tested **standalone** with its own `vendor/` (do not run composer in the host app).

## Hard rules

1. **Author is Fady Mondy only.** The global git identity is already `Fady Mondy <EngFadyMondy@gmail.com>`.
   Never add `Co-Authored-By`, "Generated with Claude", or any Claude/AI mention to commits, tags or release notes.
2. **Never push unless** `vendor/bin/pint --test` passes **and** `vendor/bin/pest` is fully green
   (no failures, no errors; `->skip()` only with a written reason) **and** `vendor/bin/phpstan analyse --level=0` has no errors.
3. Push directly to `master` (the default branch); preserve the previous line on a branch first (step 1).
4. Do not touch `dependabot/*` branches. Do not force-push. Do not delete tags or branches.
5. If a package cannot be made green (abandoned dependency, needs a product decision), **do not push**; stop and report why.

## Steps per package

```bash
cd /e/Sites/tomatophp/packages/<repo>
git checkout master && git pull --ff-only
```

**1. Preserve the old line.** Filament ^4 code → branch `v4`; Filament ^3 code → branch `v3`.
`git branch <name>` (skip if it exists locally or on origin), push it in step 9.

**2. Filament v3 packages only — run the v4 rector while v3 is still installed.**
```bash
cp composer.json /tmp/<repo>.composer.json
rm -f composer.lock && composer.bat update --no-interaction
composer.bat require filament/upgrade:"~4.0" --dev -W --no-interaction
php vendor/bin/filament-v4 src          # also pass other dirs holding Filament PHP, comma separated: "src,tests"
cp /tmp/<repo>.composer.json composer.json
```

**3. Bump composer.json.** `php ../../tools/bump-composer.php . 5.0.0` — resolve every `WARN` line in code:
- `filament/spatie-laravel-translatable-plugin` → `lara-zeus/spatie-translatable` ^2.0 (`LaraZeus\SpatieTranslatable\...`, plugin `SpatieTranslatablePlugin`, resource trait `Translatable`, page actions `LocaleSwitcher`).
- `creagia/filament-code-field` → `Filament\Forms\Components\CodeEditor`.
- `awcodes/filament-tiptap-editor` → `Filament\Forms\Components\RichEditor`.
- `livewire/volt` → plain `Livewire\Component` classes (Volt is folded into Livewire 4).

**4. Install v5 and run the v5 rector.**
```bash
rm -f composer.lock && composer.bat update --no-interaction
cp composer.json /tmp/<repo>.composer.json
composer.bat require filament/upgrade:"~5.0" --dev -W --no-interaction
php vendor/bin/filament-v5 src
cp /tmp/<repo>.composer.json composer.json && composer.bat update --no-interaction
```
A tomatophp dependency that is not yet released as `^5.0` on Packagist means you are ahead of the
dependency order — stop and report; do not add path repositories to the package.

**5. Manual fixes the rectors miss** (v3 → v4 is the big one; see https://filamentphp.com/docs/4.x/upgrade-guide):
- `form(Form $form): Form` / `infolist(Infolist $infolist): Infolist` → `(Schema $schema): Schema` (`Filament\Schemas\Schema`), `->schema([...])` → `->components([...])`.
- Layout components moved to `Filament\Schemas\Components\*` (Section, Grid, Tabs, Tab, Fieldset, Wizard, Step, Group, Split→Flex, View, Html); `Filament\Forms\Get/Set` → `Filament\Schemas\Components\Utilities\Get/Set`.
- All actions are `Filament\Actions\*` (no more `Tables\Actions`, `Forms\Components\Actions`, `Infolists\Components\Actions`, `Notifications\Actions`, `Pages\Actions`).
  Tables: `->actions()` → `->recordActions()`, `->bulkActions()` → `->toolbarActions()`.
- Property types: `$navigationIcon` `string|BackedEnum|null`, `$navigationGroup` `string|UnitEnum|null`, page `$view` is non-static `protected string $view`.
- Enums renamed: `ActionSize`→`Size`, `MaxWidth`→`Width`, `IconPosition` etc. stay in `Filament\Support\Enums`.
- `Placeholder` is deprecated → `TextEntry` in schemas. File uploads default to private visibility — set `->visibility('public')` where the package expects public URLs.
- Custom themes/CSS: Tailwind v4 (`@import`/`@source`), no `tailwind.config.js` presets.
- Livewire 4: self-close `<livewire:x />` tags, `wire:model.blur/.change` → `wire:model.live.blur/.change` if live sync was intended, `wire:transition.*` modifiers removed, `Livewire::setUpdateRoute(fn ($handle, $path) => Route::post($path, $handle))`, Volt → `Livewire\Component`.

**6. Static check.** `vendor/bin/phpstan analyse src --level=0 --memory-limit=2G --no-progress`
(create `phpstan.neon.dist` with `includes: [vendor/larastan/larastan/extension.neon]` + `paths: [src]` if the package has none).
Fix every unknown class / undefined method / wrong argument error.

**7. Tests.** Every package must end with a real suite under `tests/` (Pest, Testbench, `tests/src/TestCase.php`
registering the Filament service providers + the package provider + a `tests/src/AdminPanelProvider.php` that registers the
package's plugin; see `packages/filament-icons/tests` for the canonical layout). Minimum coverage:
- `DebugTest`: `expect(['dd', 'dump', 'ray'])->each->not->toBeUsed();`
- the service provider boots and the plugin registers on the panel,
- each Filament Resource: list / create / edit (and view if present) pages render for an authenticated user
  (`livewire(ListX::class)->assertSuccessful()`), create + edit round-trip for at least one resource,
- every Filament Page / Widget renders, every artisan command the package ships runs.
Keep existing tests; update them to v5 APIs rather than deleting them.

**8. Housekeeping.**
- `cp ../../tools/stubs/tests.yml .github/workflows/tests.yml` (Laravel 12/13 × PHP 8.3/8.4).
- `.gitignore` must contain `vendor/`, `.phpunit.cache` (keep `composer.lock` ignored if it already is; if `composer.lock` is tracked, commit the updated one).
- README: update the compatibility/version table and install instructions to Filament v5 / Laravel 12–13. CHANGELOG.md (if present): prepend a `v5.0.0` entry.
- `vendor/bin/pint` then `vendor/bin/pint --test`.

**9. Commit, push, release.**
```bash
vendor/bin/pint --test && vendor/bin/phpstan analyse src --level=0 --memory-limit=2G --no-progress && vendor/bin/pest
git add -A && git status --short          # no vendor/, no .phpunit.cache, no tmp files
git commit -m "Upgrade to Filament v5 and Laravel 13"
git push origin <v3|v4>                    # the preserved line from step 1
git push origin master
git tag v5.0.0 && git push origin v5.0.0
gh release create v5.0.0 --repo tomatophp/<repo> --title "v5.0.0" --notes "<short bullet summary; no AI mention>"
```
`composer.json` `"version"` must equal the tag (`5.0.0`), otherwise Packagist skips the tag.

**10. Wait for Packagist** (dependents need it):
`curl -s https://repo.packagist.org/p2/tomatophp/<repo>.json | grep -o '"version":"v\?5.0.0"'` — poll every 30s, up to 10 minutes.

**11. Report**: repo, old version → new tag, test count, phpstan status, manual changes worth knowing, anything left undone.

## Release gate additions (required, added after the first releases)

**A. Issues and PRs come first (before step 9).**
- `gh issue list --repo tomatophp/<repo> --state open` and `gh pr list --repo tomatophp/<repo> --state open`.
- Bugs and reasonable features: implement them with a regression test. Prove the test is real: `git stash push -- src`, run the test (it must FAIL), `git stash pop`, run it again (it must PASS).
- Community PRs that are correct: merge them locally so the author keeps credit (`git fetch origin pull/<n>/head:pr-<n> && git merge --no-ff pr-<n>`), fix conflicts, add tests. The push marks the PR merged.
- Superseded or incorrect PRs: close with a short, kind explanation (`gh pr close <n> --comment "..."`).
- Fixed issues: reference them in the commit message (`Fixes #n`) and after the release comment on each with the release link.
- Dependabot PRs: after your push run `bash /e/Sites/tomatophp/tools/pr-sweep.sh <repo> --apply`.
- "Support Filament v5" style issues: close them with the release link.

**B. Real-project verification (before step 9).** Testbench alone is not enough.
```bash
bash /e/Sites/tomatophp/tools/make-sandbox.sh <your-agent-letter>   # once; a copy of the demo app (Laravel 13 + Filament 5)
cd /e/Sites/tomatophp-sandboxes/<letter>
composer.bat require "tomatophp/<repo>:~5.0" -W --no-interaction    # resolves from the local packages/ clone
php artisan <the package install command from its README>
php artisan migrate --force
# register the plugin in app/Providers/Filament/AdminPanelProvider.php as the README says
php artisan test --filter=FilamentPanelSmokeTest                      # every panel page must render
```
Every page the package adds must show up as rendered in the smoke output, not skipped (add a factory or a seeder row if a record page is skipped).

**C. Cover and screenshots (before step 9).** Replace the old-logo README cover `arts/fadymondy-tomato-*.jpg`
(keep the same file name) with the new TomatoPHP style:
```bash
cd /e/Sites/tomatophp-sandboxes/<letter> && php artisan db:seed --force && php artisan serve --port=<8100 + agent number> &
cd /e/Sites/tomatophp/tools/screenshots
# write a config like covers.json with baseUrl http://127.0.0.1:<port> and the package's pages, then:
SHOT_EMAIL=demo@tomatophp.com SHOT_PASSWORD=demo1234 node shoot.mjs <your-config>.json
```
Render the cover with `/e/Sites/tomatophp/tools/banner/template.html` (params `title`, `desc`, `pkg`, `badge`, `shot` = a dark screenshot)
through headless Chrome at 2560x1440 (see how `filament-tomatophp-theme/arts/tomatophp-theme.jpg` was made), convert to JPG with PHP GD
using forward-slash paths, and refresh the README screenshots with the new light/dark captures.

**D. Demo safety notes.** In your report, list anything the public demo must lock down for this package: settings fields that hold
secrets (API keys, SMTP, payment gateways), actions that run commands or touch files, impersonation, emails sent to real addresses.
Never commit to `E:\Sites\tomatophp` itself; describe the plugin registration, install command and a suggested seeder instead.

## Testbench gotcha: provider order

In a Testbench `TestCase`, register `Filament\Support\SupportServiceProvider` before `LivewireServiceProvider` (`sort()` the provider list as filament-alerts does). Otherwise Filament's `bind(DataStore)` replaces Livewire's shared store and every Livewire render fails with `ViewErrorBag::put(): null given`. Real apps are not affected.
