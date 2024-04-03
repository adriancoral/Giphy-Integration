<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\RequestHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->state(['email' => 'joe@gmail.com'])->create();
        $this->token = $this->createToken($this->user);
    }

    /** @test */
    public function a_logged_user_can_save_a_favorite()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('favorite.add'), [
                'gif_id' => 'L1cdLhPrp9wAL1CbQU',
                'alias' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', true)
                    ->where('payload.alias', 'test')
                    ->where('payload.gif_id', 'L1cdLhPrp9wAL1CbQU')
                    ->where('payload.user_id', $this->user->id)
                    ->etc();
            }
        );

        $this->assertCount(1, Favorite::all());
        $this->assertCount(1, RequestHistory::all());

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'alias' => 'test',
            'gif_id' => 'L1cdLhPrp9wAL1CbQU',
        ]);
    }

    /** @test */
    public function a_logged_user_cannot_duplicate_favorite()
    {
        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('favorite.add'), [
                'gif_id' => 'L1cdLhPrp9wAL1CbQU',
                'alias' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $this->assertCount(1, Favorite::all());

        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('favorite.add'), [
                'gif_id' => 'L1cdLhPrp9wAL1CbQU',
                'alias' => 'saved Again',
            ], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $this->assertCount(1, Favorite::all());
        $this->assertCount(2, RequestHistory::all());

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'alias' => 'saved Again',
            'gif_id' => 'L1cdLhPrp9wAL1CbQU',
        ]);
    }

    /** @test */
    public function add_favorite_required_alias_and_gif_id_fields()
    {
        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('favorite.add'), [
                'gifId' => 'test',
                'alias' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertCount(1, RequestHistory::all());

        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('favorite.add'), [
                'gif_id' => 'test',
                'name' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertCount(2, RequestHistory::all());

        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('favorite.add'), [], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertCount(3, RequestHistory::all());
    }

    /** @test */
    public function only_an_logged_user_can_access_add_favorite()
    {
        $this->json('POST', route('favorite.add'), [
            'gif_id' => 'R8bdOiPrp9aPO2xkMT',
            'alias' => 'test',
        ], ['Accept' => 'application/json'])
            ->assertStatus(401);

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function a_logged_user_can_see_your_favorite_list()
    {
        $otherUser = User::factory()->create();

        Favorite::factory()->count(5)->user($this->user)->create();

        Favorite::factory()->count(3)->user($otherUser)->create();

        $this->assertCount(8, Favorite::all());

        $response = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('GET', route('favorite.index'), [], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', true)
                    ->count('payload', 5)
                    ->etc();
            }
        );

        $this->assertCount(2, User::all());

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function a_logged_user_only_can_see_your_favorite_list()
    {
        Favorite::factory()->count(5)->user($this->user)->create();

        $this->assertCount(5, Favorite::all());

        $otherUser = User::factory()->create();
        $otherToken = $this->createToken($otherUser);

        $this->assertCount(2, User::all());

        $otherResponse = $this->withHeaders(['Authorization' => 'Bearer '.$otherToken])
            ->json('GET', route('favorite.index'), [], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $otherResponse->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', true)
                    ->count('payload', 0)
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function only_an_logged_user_can_access_favorite_index()
    {
        Favorite::factory()->count(2)->user($this->user)->create();

        $this->json('GET', route('favorite.index'), [], ['Accept' => 'application/json'])
            ->assertStatus(401);

        $this->assertCount(1, RequestHistory::all());
    }
}
