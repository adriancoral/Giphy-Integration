<?php

namespace Unit\Requests;

use App\Http\Requests\UserRegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterRequestTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new UserRegisterRequest();
    }


    public function test_Rules()
    {
        $this->assertEquals(
            [
                'name' => ['required', 'string'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'string', 'min:6'],
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
