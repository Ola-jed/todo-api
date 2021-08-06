<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Tests for api auth routes
 */
class AuthTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    /**
     * Testing the register route for our api
     * Http 200 and json structure
     */
    public function testRegister(): void
    {
        $password = $this->faker->password;
        $this->postJson('/api/signup', [
            'name'        => $this->faker->name,
            'email'       => $this->faker->safeEmail,
            'password1'   => $password,
            'password2'   => $password,
            'device_name' => $this->faker->linuxPlatformToken
        ])
            ->assertOk()
            ->assertJsonStructure([
                'user',
                'token'
            ]);
    }

    /**
     * Testing the login route of our api
     * Http 200 and json structure
     * Normal auth case and auth with incorrect password
     */
    public function testLogin(): void
    {
        $user = User::factory()->create();
        $this->postJson('/api/signin', [
            'email'       => $user->email,
            'password'    => '0000',
            'device_name' => $this->faker->linuxPlatformToken
        ])
            ->assertOk()
            ->assertJsonStructure([
                'user',
                'token'
            ]);
        $this->postJson('/api/signin', [
            'email'       => $user->email,
            'password'    => 'wrongPassword',
            'device_name' => $this->faker->linuxPlatformToken
        ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertExactJson(['message' => 'Auth failed']);
    }

    /**
     * Testing logout route
     */
    public function testLogout(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->post('/api/logout')->assertExactJson(['message' => 'Logout successful']);
    }
}
