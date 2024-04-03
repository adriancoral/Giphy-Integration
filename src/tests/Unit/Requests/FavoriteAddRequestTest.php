<?php

namespace Unit\Requests;

use App\Http\Requests\FavoriteAddRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteAddRequestTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new FavoriteAddRequest();
    }


    public function test_Rules()
    {
        $this->assertEquals(
            [
                'alias' => ['required', 'string'],
                'gif_id' => ['required', 'string'],
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
