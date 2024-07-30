<?php

namespace App\Providers;

use App\Policies\NotePolicy;
use App\Policies\TranslationPolicy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\DatabaseCreated;
use Stancl\Tenancy\Events\DatabaseMigrated;
use Stancl\Tenancy\Events\SyncedResourceChangedInForeignDatabase;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenantCreated;
use TomatoPHP\FilamentNotes\Models\Note;
use TomatoPHP\FilamentTranslations\Models\Translation;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');

        Event::listen(DatabaseMigrated::class, function ($data){
            config(['database.connections.dynamic.database' => config('tenancy.database.prefix').$data->tenant->id. config('tenancy.database.suffix')]);
            DB::connection('dynamic')
                ->table('users')
                ->insert([
                    "name" => $data->tenant->name,
                    "email" => $data->tenant->email,
                    "password" => $data->tenant->password,
                    "packages" => json_encode($data->tenant->packages),
                    "email_verified_at" => Carbon::now(),
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
        });

        Event::listen(SyncedResourceChangedInForeignDatabase::class, function ($data){
            config(['database.connections.dynamic.database' => $data->tenant->tenancy_db_name]);
            DB::connection('dynamic')
                ->table('users')
                ->where('email', $data->model->email)
                ->update([
                    "name" => $data->model->name,
                    "email" => $data->model->email,
                    "packages" => json_encode($data->model->packages),
                    "password" => $data->model->password,
                ]);
        });


        Gate::policy(Note::class, NotePolicy::class);
    }
}
