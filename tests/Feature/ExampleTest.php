<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Chirp;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    public function test_the_application_returns_a_successful_response(): void
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $reponse = $this->post('/chirps', [
            'content' => 'Mon premier chirp !'
        ]);

        $reponse->assertStatus(201);
        $this->assertDatabaseHas('chirps', [
            'content' => 'Mon premier chirp !',
            'user_id' => $utilisateur->id,
        ]);
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}