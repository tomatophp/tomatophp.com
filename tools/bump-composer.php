<?php

/**
 * Bump a tomatophp package's composer.json to Filament v5 / Laravel 13.
 *
 * Usage: php tools/bump-composer.php <package-dir> [version=5.0.0]
 *
 * Deterministic changes only. Anything that needs a code change (replaced or
 * abandoned third-party packages) is reported as a WARN line, not rewritten.
 */
$dir = rtrim($argv[1] ?? '', '/\\');
$version = $argv[2] ?? '5.0.0';
$file = $dir . '/composer.json';

if (! is_file($file)) {
    fwrite(STDERR, "No composer.json in [{$dir}]\n");
    exit(1);
}

$json = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

$filamentOfficial = '/^filament\/(filament|support|forms|tables|notifications|actions|infolists|widgets|schemas|query-builder|spatie-laravel-[a-z-]+-plugin)$/';

$needsManualWork = [
    'filament/spatie-laravel-translatable-plugin' => 'v3 only; replace with lara-zeus/spatie-translatable ^2.0 (namespace LaraZeus\\SpatieTranslatable)',
    'creagia/filament-code-field' => 'v3 only; replace with Filament\\Forms\\Components\\CodeEditor',
    'awcodes/filament-tiptap-editor' => 'v3 only; replace with Filament\\Forms\\Components\\RichEditor',
    'protonemedia/laravel-splade' => 'abandoned, no Laravel 13 support',
    'livewire/volt' => 'folded into Livewire 4; drop Volt and use Livewire\\Component',
    'devdojo/app' => 'unmaintained; verify Filament 5 compatibility',
    'devdojo/themes' => 'unmaintained; verify Filament 5 compatibility',
];

$thirdParty = [
    'livewire/livewire' => '^4.0',
    'laravel/jetstream' => '^5.0',
    'laravel/sanctum' => '^4.0',
    'laravel/socialite' => '^5.20',
    'laravel/folio' => '^1.1',
    'maatwebsite/excel' => '^3.1.64|^4.0',
    'lara-zeus/spatie-translatable' => '^2.0',
    'bezhansalleh/filament-shield' => '^4.0',
    'stechstudio/filament-impersonate' => '^5.0',
    'spatie/laravel-medialibrary' => '^11.13',
    'bavix/laravel-wallet' => '^11.0|^12.0',
    'nwidart/laravel-modules' => '^12.0|^13.0',
    'laravelcm/laravel-subscriptions' => '^1.5',
    'stancl/tenancy' => '^3.9',
    'laravel-notification-channels/fcm' => '^5.0|^6.0',
    'mikebronner/laravel-model-caching' => '^12.0|^13.0',
];

$devDefaults = [
    'larastan/larastan' => '^3.0',
    'laravel/pint' => '^1.24',
    'nunomaduro/collision' => '^8.6',
    'orchestra/testbench' => '^10.0|^11.0',
    'pestphp/pest' => '^4.0|^5.0',
    'pestphp/pest-plugin-arch' => '^4.0|^5.0',
    'pestphp/pest-plugin-laravel' => '^4.0|^5.0',
    'pestphp/pest-plugin-livewire' => '^4.0|^5.0',
    'pestphp/pest-plugin-type-coverage' => '^4.0|^5.0',
    'phpstan/extension-installer' => '^1.4',
    'phpstan/phpstan-deprecation-rules' => '^2.0',
    'phpstan/phpstan-phpunit' => '^2.0',
];

// The minimum toolchain every package must have so it can be tested.
// Larastan is what catches Filament classes removed or renamed by the upgrade (phpstan level 0).
$devRequired = ['larastan/larastan', 'laravel/pint', 'orchestra/testbench', 'pestphp/pest', 'pestphp/pest-plugin-laravel', 'pestphp/pest-plugin-livewire'];

$changes = [];
$warnings = [];

$require = $json['require'] ?? [];

foreach ($require as $name => $constraint) {
    $new = $constraint;

    if ($name === 'php') {
        $new = '^8.2';
    } elseif (preg_match($filamentOfficial, $name)) {
        $new = '^5.0';
    } elseif (str_starts_with($name, 'tomatophp/filament-')) {
        $new = '^5.0';
    } elseif ($name === 'laravel/framework' || str_starts_with($name, 'illuminate/')) {
        $new = str_contains($constraint, '13') ? $constraint : '^12.0|^13.0';
    } elseif (isset($thirdParty[$name])) {
        $new = $thirdParty[$name];
    }

    if (isset($needsManualWork[$name])) {
        $warnings[] = "{$name}: {$needsManualWork[$name]}";
    }

    if ($new !== $constraint) {
        $changes[] = "require {$name}: {$constraint} -> {$new}";
        $require[$name] = $new;
    }
}

$json['require'] = $require;

$requireDev = $json['require-dev'] ?? [];

foreach ($devRequired as $name) {
    $requireDev[$name] ??= $devDefaults[$name];
}

foreach ($requireDev as $name => $constraint) {
    if (isset($devDefaults[$name]) && $devDefaults[$name] !== $constraint) {
        $changes[] = "require-dev {$name}: {$constraint} -> {$devDefaults[$name]}";
        $requireDev[$name] = $devDefaults[$name];
    }
}

// Pest 4+ pulls in its own plugins; phpstan 1.x helpers no longer resolve with larastan 3.
ksort($requireDev);
$json['require-dev'] = $requireDev;

$json['config']['sort-packages'] = true;
$json['config']['allow-plugins']['pestphp/pest-plugin'] = true;

if (isset($requireDev['phpstan/extension-installer'])) {
    $json['config']['allow-plugins']['phpstan/extension-installer'] = true;
}

if (in_array($json['minimum-stability'] ?? 'stable', ['dev', 'alpha', 'beta', 'RC'], true)) {
    $changes[] = "minimum-stability: {$json['minimum-stability']} -> removed";
    unset($json['minimum-stability']);
}

if (($json['version'] ?? null) !== $version) {
    $changes[] = 'version: ' . ($json['version'] ?? '(none)') . " -> {$version}";
    $json['version'] = $version;
}

file_put_contents(
    $file,
    json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n",
);

foreach ($changes as $line) {
    echo "  {$line}\n";
}

foreach ($warnings as $line) {
    echo "  WARN {$line}\n";
}
