<?php namespace Tests\Feature\api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'password' => Hash::make('password')
        ]);
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertCreated()->assertJsonStructure(['token']);
    }

    public function test_user_can_login(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ]);

        $response->assertOk()->assertJsonStructure(['token']);
    }

    public function test_authenticated_user_can_access_protected_routes(): void
    {
        $user = $this->authenticate($this->user);

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJson([
                'email' => $user->email,
            ]);
    }

    public function test_user_can_logout(): void
    {
        $user = $this->authenticate($this->user);
        $token = $user->createToken('testToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertOk()->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }
}
