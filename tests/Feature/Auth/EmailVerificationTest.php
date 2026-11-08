<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_users_can_use_the_forum_without_verifying_their_email(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->assertFalse($user instanceof MustVerifyEmail);
        $this->assertFalse(Route::has('verification.notice'));
        $this->assertFalse(Route::has('verification.verify'));
        $this->assertFalse(Route::has('verification.send'));

        $this->actingAs($user)->get(route('threads.index'))->assertOk();
    }
}
