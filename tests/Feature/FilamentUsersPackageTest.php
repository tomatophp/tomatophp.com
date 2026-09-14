<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use TomatoPHP\FilamentUsers\Filament\Resources\Users\Pages\CreateUser;
use TomatoPHP\FilamentUsers\Filament\Resources\Users\Pages\ListUsers;

/**
 * tomatophp/filament-users inside a real app whose User model uses Laravel's `hashed` cast
 * (the package's own suite runs without it). Covers tomatophp/filament-users#58 and #60.
 */
class FilamentUsersPackageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    #[Test]
    public function creating_a_user_stores_a_password_that_can_log_in(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'Secret-Pass-123',
                'passwordConfirmation' => 'Secret-Pass-123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('Secret-Pass-123', User::where('email', 'jane@example.com')->value('password')));
    }

    #[Test]
    public function change_password_action_sets_the_given_password_without_double_hashing(): void
    {
        $user = User::factory()->create();

        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('changePassword')->table($user), data: [
                'password' => 'Brand-New-Pass-456',
                'passwordConfirmation' => 'Brand-New-Pass-456',
            ])
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('Brand-New-Pass-456', $user->refresh()->password));
    }

    #[Test]
    public function change_password_action_generates_a_working_password_when_left_blank(): void
    {
        $user = User::factory()->create();
        $original = $user->password;

        Livewire::test(ListUsers::class)
            ->callAction(TestAction::make('changePassword')->table($user))
            ->assertHasNoErrors()
            ->assertNotified();

        $this->assertNotSame($original, $user->refresh()->password);
        $this->assertTrue(Hash::isHashed($user->password));
    }
}
