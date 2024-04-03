<?php

namespace Unit\Listeners;

use App\Events\RequestTerminated;
use App\Listeners\StoreRequest;
use App\Models\RequestHistory;
use Tests\TestCase;

class StoreRequestTest extends TestCase
{
    /** @test */
    public function store_request_listener_save_request_data()
    {
        $data = [
            'user_id' => 1,
            'route_name' => 'user.login',
            'request_body' => [],
            'response_body' => [],
            'response_code' => 200,
            'user_ip' => '192.68.200.5'
        ];

        $event = new RequestTerminated($data);

        (new StoreRequest())->handle($event);

        $this->assertCount(1, RequestHistory::all());

        $this->assertDatabaseHas('request_histories', [
            'user_id' => 1,
            'route_name' => 'user.login',
            'user_ip' => '192.68.200.5',
        ]);
    }
}
