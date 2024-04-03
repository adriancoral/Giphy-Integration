<?php

namespace Tests\Feature;

use App\Exceptions\AuthorizationException;
use App\Models\RequestHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /** @test */
    public function a_registered_user_can_login(): void
    {
        $user = User::factory()->state(['email' => 'doe@sample.com'])->create();

        $this->assertCount(1, User::all());

        $response = $this->json(
            'POST',
            route('user.login'),
            ['email' => $user->email, 'password' =>  'password'],
            ['Accept' => 'application/json']
        )
            ->assertStatus(200);

        $response->assertJson(
            function (AssertableJson $json) use ($user) {
                return $json->where('success', true)
                    ->where('payload.id', $user->id)
                    ->where('payload.token_type', 'bearer')
                    ->has('payload.token')
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function email_and_password_fields_are_required_to_login(): void
    {
        $response = $this->json(
            'POST',
            route('user.login'),
            ['email' => 'sara@mail.com'],
            ['Accept' => 'application/json']
        )
            ->assertStatus(422);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('message', 'The password field is required.')
                    ->has('errors.password')
                    ->etc();
            }
        );

        $response = $this->json(
            'POST',
            route('user.login'),
            ['password' => 'secret'],
            ['Accept' => 'application/json']
        )
            ->assertStatus(422);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('message', 'The email field is required.')
                    ->has('errors.email')
                    ->etc();
            }
        );

        $this->assertCount(2, RequestHistory::all());
    }

    /** @test */
    public function trying_to_login_without_the_required_fields_throws_an_exception()
    {
        $this->withoutExceptionHandling();

        $this->expectException(ValidationException::class);

        $this->json(
            'POST',
            route('user.login'),
            ['email' => 'sara@mail.com'],
            ['Accept' => 'application/json']
        )
            ->assertStatus(422);

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function a_unregistered_user_cannot_login_error_expected(): void
    {
        $this->assertCount(0, User::all());

        $response = $this->json(
            'POST',
            route('user.login'),
            ['email' => 'other@mail.com', 'password' =>  'password'],
            ['Accept' => 'application/json']
        )
            ->assertStatus(401);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', false)
                    ->where('message', 'Authentication fail')
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function a_logged_user_can_logout()
    {
        $user = User::factory()->create();

        $this->assertCount(1, User::all());

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$this->createToken($user)])
            ->json('POST', route('user.logout'), [], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', true)
                    ->where('payload', 'Logged out successfully')
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function an_exception_is_expected_when_unauthenticated_user_try_logout(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthorizationException::class);

        $this->json('POST', route('user.logout'), [], ['Accept' => 'application/json']);
    }

    /** @test */
    public function an_logged_user_can_get_your_info_through_the_endpoint_me()
    {
        User::factory()->count(3)->create();
        $user = User::factory()->create();

        $this->assertCount(4, User::all());

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$this->createToken($user)])
            ->json('GET', route('user.me'), [], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $response->assertJson(
            function (AssertableJson $json) use ($user) {
                return $json->where('success', true)
                    ->where('payload.id', $user->id)
                    ->where('payload.name', $user->name)
                    ->where('payload.email', $user->email)
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function an_exception_is_expected_when_unauthenticated_user_try_access_to_endpoint_me(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthorizationException::class);

        $this->json('GET', route('user.me'), [], ['Accept' => 'application/json']);
    }

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->json(
            'POST',
            route('user.register'),
            ['name' => 'Joe', 'email' => 'joe@gmail.com', 'password' =>  'secretpass'],
            ['Accept' => 'application/json']
        )
            ->assertStatus(200);

        $this->assertCount(1, User::all());

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', true)
                    ->where('payload.token_type', 'bearer')
                    ->has('payload.id')
                    ->has('payload.token')
                    ->etc();
            }
        );

        $this->assertDatabaseHas('users', [
            'name' => 'Joe',
            'email' => 'joe@gmail.com',
        ]);

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function when_a_user_registers_their_email_must_be_unique()
    {
        User::factory()->state(['email' => 'doe@hotmail.com'])->create();

        $this->assertCount(1, User::all());

        $response = $this->json(
            'POST',
            route('user.register'),
            ['name' => 'Joe', 'email' => 'doe@hotmail.com', 'password' =>  'secretpass'],
            ['Accept' => 'application/json']
        )
            ->assertStatus(422);

        $this->assertCount(1, User::all());

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('message', 'The email has already been taken.')
                    ->has('errors.email')
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }
}
