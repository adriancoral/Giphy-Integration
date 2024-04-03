<?php

namespace Unit\Requests;

use App\Http\Requests\GiphyGifsRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GiphyGifsRequestTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new GiphyGifsRequest();
    }


    public function test_Rules()
    {
        $this->assertEquals(
            [
                'ids' => ['required', 'string'],
                'rating' => ['filled', 'string'],
        ],
            $this->subject->rules()
        );
    }

    /** @test */
    public function testAuthorize()
    {
        $this->assertTrue($this->subject->authorize());
    }
}
