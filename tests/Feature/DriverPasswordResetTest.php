<?php

use App\Mail\PasswordResetCodeMail;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function makePasswordResetDriver(): Driver
{
    $user = User::create([
        'name' => 'Reset Driver',
        'email' => 'reset-' . uniqid() . '@example.com',
        'password' => Hash::make('password123'),
        'role' => 'driver',
        'status' => 'active',
    ]);

    return Driver::create([
        'user_id' => $user->id,
        'badge_id' => 'BDG-' . uniqid(),
        'duty_status' => 'available',
    ]);
}

it('emails a reset code to a driver', function (): void {
    Mail::fake();

    $driver = makePasswordResetDriver();

    $this->postJson('/api/forgot-password', ['email' => $driver->user->email])
        ->assertOk()
        ->assertJsonPath('message', 'If that email belongs to a driver account, a password reset code has been sent.');

    Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) use ($driver): bool {
        return $mail->hasTo($driver->user->email) && preg_match('/^\d{6}$/', $mail->code) === 1;
    });

    expect(DB::table('password_reset_tokens')->where('email', $driver->user->email)->exists())->toBeTrue();
});

it('does not reveal whether an email exists', function (): void {
    Mail::fake();

    $this->postJson('/api/forgot-password', ['email' => 'nobody@example.com'])
        ->assertOk()
        ->assertJsonPath('message', 'If that email belongs to a driver account, a password reset code has been sent.');

    Mail::assertNothingSent();
    expect(DB::table('password_reset_tokens')->count())->toBe(0);
});

it('resets the password using the emailed code', function (): void {
    Mail::fake();

    $driver = makePasswordResetDriver();
    $code = null;

    $this->postJson('/api/forgot-password', ['email' => $driver->user->email])->assertOk();

    Mail::assertSent(PasswordResetCodeMail::class, function (PasswordResetCodeMail $mail) use (&$code): bool {
        $code = $mail->code;

        return true;
    });

    $this->postJson('/api/reset-password', [
        'email' => $driver->user->email,
        'code' => $code,
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Your password has been reset. Please sign in with your new password.');

    expect(Hash::check('brand-new-password', $driver->user->fresh()->password))->toBeTrue();
    expect(DB::table('password_reset_tokens')->where('email', $driver->user->email)->exists())->toBeFalse();
});

it('rejects an incorrect reset code', function (): void {
    $driver = makePasswordResetDriver();

    DB::table('password_reset_tokens')->insert([
        'email' => $driver->user->email,
        'token' => Hash::make('111111'),
        'created_at' => now(),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => $driver->user->email,
        'code' => '000000',
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('code');
});

it('rejects an expired reset code', function (): void {
    $driver = makePasswordResetDriver();

    DB::table('password_reset_tokens')->insert([
        'email' => $driver->user->email,
        'token' => Hash::make('222222'),
        'created_at' => now()->subMinutes(120),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => $driver->user->email,
        'code' => '222222',
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('code');
});
