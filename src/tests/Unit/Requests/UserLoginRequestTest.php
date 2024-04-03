<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginRequestTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new UserLoginRequest();
    }


    public function test_Rules()
    {
        $this->assertEquals(
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
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
