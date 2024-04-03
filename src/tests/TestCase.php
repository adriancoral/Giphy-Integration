<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();
        $this->passpoortClientAccess();
    }

    protected function passpoortClientAccess()
    {
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null,
            'Test Personal Access Client',
            'http://localhost'
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    protected function createToken(User $user): string
    {
        return $user->createToken('appToken')->accessToken;
    }
}
