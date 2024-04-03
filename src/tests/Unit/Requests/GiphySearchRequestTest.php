<?php

namespace Unit\Requests;

use App\Http\Requests\GiphySearchRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GiphySearchRequestTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new GiphySearchRequest();
    }


    public function test_Rules()
    {
        $this->assertEquals(
            [
                'q' => ['required', 'string'],
                'limit' => ['filled', 'integer'],
                'offset' => ['filled', 'integer'],
                'rating' => ['filled', 'string'],
                'lang' => ['filled', 'string'],
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
