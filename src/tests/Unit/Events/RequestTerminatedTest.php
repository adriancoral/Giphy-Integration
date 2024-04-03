<?php

namespace Unit\Events;

use App\Events\RequestTerminated;
use App\Listeners\StoreRequest;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RequestTerminatedTest extends TestCase
{
    /** @test */
    public function request_terminated_event_listened_by_storerequest()
    {
        Event::fake();

        $data = [
            'user_id' => 1,
            'route_name' => 'user.login',
            'request_body' => [],
            'response_body' => [],
            'response_code' => 200,
            'user_ip' => '192.68.200.5'
        ];

        RequestTerminated::dispatch($data);

        Event::assertDispatched(function (RequestTerminated $event) use ($data) {
            $this->assertEquals($event->data()['user_id'], $data['user_id']);
            $this->assertEquals($event->data()['route_name'], $data['route_name']);
            $this->assertEquals($event->data()['response_code'], $data['response_code']);
            $this->assertEquals($event->data()['user_ip'], $data['user_ip']);
            return true;
        });

        Event::assertListening(
            RequestTerminated::class,
            StoreRequest::class
        );
    }
}
