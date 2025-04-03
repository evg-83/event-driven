<?php namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function authenticate(User $user = null): User
    {
        $user = $user ?? User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return $user;
    }
}
