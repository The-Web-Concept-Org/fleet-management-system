<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_requires_authentication(): void
    {
        $response = $this->get(route('settings'));
        $response->assertRedirect(route('login'));
    }

    public function test_settings_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Alice Admin',
            'email' => 'alice@lancotri.local',
        ]);

        $response = $this->actingAs($user)->get(route('settings'));

        $response->assertOk();
        $response->assertSee('Account Settings');
        $response->assertSee('Alice Admin');
        $response->assertSee('alice@lancotri.local');
    }

    public function test_user_can_update_profile_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@lancotri.local',
        ]);

        $response = $this->actingAs($user)->put(route('settings.profile'), [
            'name' => 'New Name',
            'email' => 'new@lancotri.local',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('profile_status');

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@lancotri.local', $user->email);
    }

    public function test_user_cannot_update_email_to_an_existing_email(): void
    {
        User::factory()->create([
            'email' => 'existing@lancotri.local',
        ]);

        $user = User::factory()->create([
            'email' => 'user@lancotri.local',
        ]);

        $response = $this->actingAs($user)->put(route('settings.profile'), [
            'name' => 'User',
            'email' => 'existing@lancotri.local',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-secret-123'),
        ]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'old-secret-123',
            'password' => 'new-secret-456',
            'password_confirmation' => 'new-secret-456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('password_status');

        $user->refresh();
        $this->assertTrue(Hash::check('new-secret-456', $user->password));
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-secret'),
        ]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'wrong-secret',
            'password' => 'new-secret-456',
            'password_confirmation' => 'new-secret-456',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_user_cannot_update_password_with_mismatched_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-secret'),
        ]);

        $response = $this->actingAs($user)->put(route('settings.password'), [
            'current_password' => 'correct-secret',
            'password' => 'new-secret-456',
            'password_confirmation' => 'mismatched-secret',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
