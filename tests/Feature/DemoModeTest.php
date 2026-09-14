<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('demo.enabled', true);
        config()->set('demo.email', 'demo@tomatophp.com');
        config()->set('demo.password', 'demo1234');
        config()->set('demo.admin.email', 'admin@tomatophp.test');
        config()->set('demo.admin.password', 'Admin-Secret-123');

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function the_seeder_creates_the_demo_admin_and_sample_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(User::where('email', 'demo@tomatophp.com')->exists());
        $this->assertTrue(User::where('email', 'admin@tomatophp.test')->exists());
        $this->assertGreaterThanOrEqual(10, User::where('email', 'like', '%@example.com')->count());
        $this->assertTrue(auth()->attempt(['email' => 'demo@tomatophp.com', 'password' => 'demo1234']));
    }

    #[Test]
    public function the_login_page_is_prefilled_with_the_demo_account(): void
    {
        Livewire::test(Login::class)
            ->assertFormSet(['email' => 'demo@tomatophp.com', 'password' => 'demo1234'])
            ->assertSee('Demo data resets every hour');
    }

    #[Test]
    public function the_login_page_is_empty_when_demo_mode_is_off(): void
    {
        config()->set('demo.enabled', false);

        Livewire::test(Login::class)->assertFormSet(['email' => null, 'password' => null]);
    }

    #[Test]
    public function protected_accounts_cannot_be_updated_deleted_or_impersonated(): void
    {
        $this->seed(DatabaseSeeder::class);

        $demo = User::where('email', 'demo@tomatophp.com')->firstOrFail();
        $admin = User::where('email', 'admin@tomatophp.test')->firstOrFail();
        $sample = User::where('email', 'like', '%@example.com')->firstOrFail();

        $this->assertFalse(Gate::forUser($demo)->allows('update', $admin));
        $this->assertFalse(Gate::forUser($demo)->allows('delete', $admin));
        $this->assertFalse(Gate::forUser($demo)->allows('update', $demo));
        $this->assertFalse($admin->canBeImpersonated());

        $this->assertTrue(Gate::forUser($demo)->allows('update', $sample));
        $this->assertTrue(Gate::forUser($demo)->allows('delete', $sample));
        $this->assertTrue($sample->canBeImpersonated());
    }

    #[Test]
    public function a_signed_in_user_cannot_change_a_protected_account_through_any_action(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@tomatophp.test')->firstOrFail();
        $this->actingAs(User::where('email', 'demo@tomatophp.com')->firstOrFail());

        $this->expectException(HttpException::class);

        $admin->update(['password' => 'Hijacked-Pass-1']);
    }

    #[Test]
    public function signing_in_still_updates_the_remember_token_of_a_protected_account(): void
    {
        $this->seed(DatabaseSeeder::class);

        $demo = User::where('email', 'demo@tomatophp.com')->firstOrFail();
        $this->actingAs($demo);

        $demo->setRememberToken('new-token');
        $demo->save();

        $this->assertSame('new-token', $demo->refresh()->getRememberToken());
    }
}
