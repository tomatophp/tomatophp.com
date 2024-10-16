<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Tenant;
use App\Policies\AccountPolicy;
use App\Policies\AccountRequestPolicy;
use App\Policies\APIResourcePolicy;
use App\Policies\AttendanceShiftPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CityPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ContactPolicy;
use App\Policies\CountryPolicy;
use App\Policies\CouponPolicy;
use App\Policies\CurrencyPolicy;
use App\Policies\EmployeeApplyPolicy;
use App\Policies\FolderPolicy;
use App\Policies\FormPolicy;
use App\Policies\GiftCardPolicy;
use App\Policies\InvoicePolicy;
use App\Policies\LanguagePolicy;
use App\Policies\LocationPolicy;
use App\Policies\MediaPolicy;
use App\Policies\MenuPolicy;
use App\Policies\NotePolicy;
use App\Policies\NotificationsLogsPolicy;
use App\Policies\NotificationsTemplatePolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PlanPolicy;
use App\Policies\PostPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ReferralCodePolicy;
use App\Policies\RolePolicy;
use App\Policies\ShippingVendorPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TenantPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\TranslationPolicy;
use App\Policies\TypePolicy;
use App\Policies\UserNotificationPolicy;
use App\Policies\WalletPolicy;
use App\Policies\WithdrawalMethodPolicy;
use App\Policies\WithdrawalRequestPolicy;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravelcm\Subscriptions\Models\Plan;
use Laravelcm\Subscriptions\Models\Subscription;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Events\DatabaseCreated;
use Stancl\Tenancy\Events\DatabaseMigrated;
use Stancl\Tenancy\Events\SyncedResourceChangedInForeignDatabase;
use Stancl\Tenancy\Events\TenancyBootstrapped;
use Stancl\Tenancy\Events\TenancyEnded;
use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Events\TenantCreated;
use TomatoPHP\FilamentAccounts\Facades\FilamentAccounts;
use TomatoPHP\FilamentAccounts\Models\AccountRequest;
use TomatoPHP\FilamentAccounts\Models\Contact;
use TomatoPHP\FilamentAccounts\Models\Team;
use TomatoPHP\FilamentAlerts\Models\NotificationsLogs;
use TomatoPHP\FilamentAlerts\Models\NotificationsTemplate;
use TomatoPHP\FilamentAlerts\Models\UserNotification;
use TomatoPHP\FilamentApi\Models\APIResource;
use TomatoPHP\FilamentBookmarksMenu\Facades\FilamentBookmarksMenu;
use TomatoPHP\FilamentBookmarksMenu\Services\Contracts\BookmarkType;
use TomatoPHP\FilamentCms\Events\PostCreated;
use TomatoPHP\FilamentCms\Events\PostDeleted;
use TomatoPHP\FilamentCms\Events\PostUpdated;
use TomatoPHP\FilamentCms\Filament\Resources\PostResource;
use TomatoPHP\FilamentCms\Models\Category;
use TomatoPHP\FilamentCms\Models\Form;
use TomatoPHP\FilamentCms\Models\Post;
use TomatoPHP\FilamentDocs\Facades\FilamentDocs;
use TomatoPHP\FilamentDocs\Filament\Actions\DocumentAction;
use TomatoPHP\FilamentDocs\Filament\RelationManager\DocumentRelationManager;
use TomatoPHP\FilamentDocs\Services\Contracts\DocsVar;
use TomatoPHP\FilamentEcommerce\Models\Company;
use TomatoPHP\FilamentEcommerce\Models\Coupon;
use TomatoPHP\FilamentEcommerce\Models\GiftCard;
use TomatoPHP\FilamentEcommerce\Models\Order;
use TomatoPHP\FilamentEcommerce\Models\Product;
use TomatoPHP\FilamentEcommerce\Models\ReferralCode;
use TomatoPHP\FilamentEcommerce\Models\ShippingVendor;
use TomatoPHP\FilamentEmployees\Models\AttendanceShift;
use TomatoPHP\FilamentEmployees\Models\EmployeeApply;
use TomatoPHP\FilamentInvoices\Facades\FilamentInvoices;
use TomatoPHP\FilamentInvoices\Models\Invoice;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFor;
use TomatoPHP\FilamentInvoices\Services\Contracts\InvoiceFrom;
use TomatoPHP\FilamentIssues\Facades\FilamentIssues;
use TomatoPHP\FilamentLocations\Models\City;
use TomatoPHP\FilamentLocations\Models\Country;
use TomatoPHP\FilamentLocations\Models\Currency;
use TomatoPHP\FilamentLocations\Models\Language;
use TomatoPHP\FilamentLocations\Models\Location;
use TomatoPHP\FilamentMediaManager\Models\Folder;
use TomatoPHP\FilamentMediaManager\Models\Media;
use TomatoPHP\FilamentMenus\Models\Menu;
use TomatoPHP\FilamentNotes\Models\Note;
use TomatoPHP\FilamentPayments\Models\Payment;
use TomatoPHP\FilamentSeo\Jobs\GoogleIndexURLJob;
use TomatoPHP\FilamentSeo\Jobs\GoogleRemoveIndexURLJob;
use TomatoPHP\FilamentTranslations\Models\Translation;
use TomatoPHP\FilamentTypes\Models\Type;
use TomatoPHP\FilamentWallet\Models\Transaction;
use TomatoPHP\FilamentWallet\Models\Wallet;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalMethod;
use TomatoPHP\FilamentWithdrawals\Models\WithdrawalRequest;
use Ymigval\LaravelIndexnow\Facade\IndexNow;

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

        Event::listen(TenancyBootstrapped::class, function($event){
            $permissionRegistrar = app(\Spatie\Permission\PermissionRegistrar::class);
            $permissionRegistrar->cacheKey = 'spatie.permission.cache.tenant.' . $event->tenancy->tenant->getTenantKey();
        });

        Event::listen(TenancyEnded::class, function($event){
            $permissionRegistrar = app(\Spatie\Permission\PermissionRegistrar::class);
            $permissionRegistrar->cacheKey = 'spatie.permission.cache';
        });

        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Tenant::class, TenantPolicy::class);
        Gate::policy(Note::class, NotePolicy::class);
        Gate::policy(Translation::class, TranslationPolicy::class);
        Gate::policy(Type::class, TypePolicy::class);
        Gate::policy(Account::class, AccountPolicy::class);
        Gate::policy(AccountRequest::class, AccountRequestPolicy::class);
        Gate::policy(APIResource::class, APIResourcePolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(City::class, CityPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(Country::class, CountryPolicy::class);
        Gate::policy(Coupon::class, CouponPolicy::class);
        Gate::policy(Currency::class, CurrencyPolicy::class);
        Gate::policy(Folder::class, FolderPolicy::class);
        Gate::policy(Form::class, FormPolicy::class);
        Gate::policy(GiftCard::class, GiftCardPolicy::class);
        Gate::policy(Language::class, LanguagePolicy::class);
        Gate::policy(Location::class, LocationPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(Menu::class, MenuPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(ReferralCode::class, ReferralCodePolicy::class);
        Gate::policy(ShippingVendor::class, ShippingVendorPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(Wallet::class, WalletPolicy::class);
        Gate::policy(UserNotification::class, UserNotificationPolicy::class);
        Gate::policy(NotificationsTemplate::class, NotificationsTemplatePolicy::class);
        Gate::policy(NotificationsLogs::class, NotificationsLogsPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(\TomatoPHP\FilamentSubscriptions\Models\Plan::class, PlanPolicy::class);
        Gate::policy(\TomatoPHP\FilamentSubscriptions\Models\Subscription::class, SubscriptionPolicy::class);
        Gate::policy(WithdrawalMethod::class, WithdrawalMethodPolicy::class);
        Gate::policy(WithdrawalRequest::class, WithdrawalRequestPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);
        Gate::policy(AttendanceShift::class, AttendanceShiftPolicy::class);
        Gate::policy(EmployeeApply::class, EmployeeApplyPolicy::class);


        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_AFTER,
            fn (): string => Blade::render('@livewire(\'quick-menu\')')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn (): string => view('layouts.js')->render()
        );


        FilamentInvoices::registerFor([
            InvoiceFor::make(Account::class)
                ->label('For Account')
        ]);

        FilamentInvoices::registerFrom([
            InvoiceFrom::make(Account::class)
                ->label('From Account')
        ]);

        RateLimiter::for('twitter', function ($job) {
            return Limit::perHour(1);
        });

        FilamentIssues::register(fn() => Post::query()->where('type', 'open-source')->pluck('meta_url')->map(fn($item) => str($item)->remove('https://github.com/')->remove('https://www.github.com/')->toString())->toArray());


        Event::listen(PostCreated::class, function ($event){
            $post = Post::query()->find($event->data['id']);

            $url = url(($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            dispatch(new GoogleIndexURLJob(
                url: $url,
            ));

            IndexNow::submit($url);
        });

        Event::listen(PostUpdated::class, function ($event){
            $post = Post::query()->find($event->data['id']);

            $url = url(($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            dispatch(new GoogleIndexURLJob(
                url: $url,
            ));

            IndexNow::submit($url);
        });

        Event::listen(PostDeleted::class, function ($event){
            $post = Post::query()->find($event->data['id']);

            $url = url(($post->type === 'post' ? '/blog/' : '/open-source/') . $post->slug);

            dispatch(new GoogleRemoveIndexURLJob(
                url: $url,
            ));
        });

    }
}
