<?php

namespace Tests\Feature;

use App\Enums\GiphyApi;
use App\Models\RequestHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GiphyControllerTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->createToken($this->user);

        $serviceResponse = $this->searchResponse();
        Http::fake([
            'http://api.giphy.com/*' => Http::response($serviceResponse, 200),
            '*' => Http::response('not found', 404)
        ]);

    }

    /** @test */
    public function giphy_search_works_properly()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('giphy.search'), [
                'q' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', true)
                    ->has('payload')
                    ->has('payload.data')
                    ->has('payload.meta')
                    ->has('payload.pagination')
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function giphy_search_required_q_field()
    {
        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('giphy.search'), [
                'query' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertCount(1, RequestHistory::all());

        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('giphy.search'), [], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertCount(2, RequestHistory::all());
    }

    /** @test */
    public function only_an_logged_user_can_access_giphy_search()
    {
        $this->json('POST', route('giphy.search'), [
                'query' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(401);

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function giphy_gifs_works_properly()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('giphy.gifs'), [
                'ids' => 'MDJ9IbxxvDUQM',
            ], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $response->assertJson(
            function (AssertableJson $json) {
                return $json->where('success', true)
                    ->has('payload')
                    ->has('payload.data')
                    ->has('payload.meta')
                    ->has('payload.pagination')
                    ->etc();
            }
        );

        $this->assertCount(1, RequestHistory::all());
    }

    /** @test */
    public function giphy_gifs_required_ids_field()
    {
        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('giphy.gifs'), [
                'id' => 'test',
            ], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertCount(1, RequestHistory::all());

        $this->withHeaders(['Authorization' => 'Bearer '.$this->token])
            ->json('POST', route('giphy.gifs'), [], ['Accept' => 'application/json'])
            ->assertStatus(422);

        $this->assertCount(2, RequestHistory::all());
    }

    /** @test */
    public function only_an_logged_user_can_access_giphy_gifs()
    {
        $this->json('POST', route('giphy.gifs'), [
            'ids' => 'test',
        ], ['Accept' => 'application/json'])
            ->assertStatus(401);

        $this->assertCount(1, RequestHistory::all());
    }

    private function searchResponse()
    {
        return [
            'data' => [
                [
                    'type' => 'gif',
                    'id' => $this->faker->bothify('???#????#???'),
                    'url' => $this->faker->imageUrl,
                ],
                [
                    'type' => 'gif',
                    'id' => $this->faker->bothify('???#????#???'),
                    'url' => $this->faker->imageUrl,
                ],
                [
                    'type' => 'gif',
                    'id' => $this->faker->bothify('???#????#???'),
                    'url' => $this->faker->imageUrl,
                ],
            ],
            'meta' => [
                'status' => 200,
                'msg' => 'OK',
                'response_id' => "keqbcagy7nerm5dyinn62fkmmni5uzb8xp69dx0y"
            ],
            'pagination' => [
                'total_count' => 5,
                'count' => 5,
                'offset' => 0
            ]
        ];
    }
}
