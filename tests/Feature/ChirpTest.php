<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Chirp;

class ChirpTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_peut_creer_un_chirp()
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
    }

    public function test_un_chirp_ne_peut_pas_avoir_un_contenu_vide()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $reponse = $this->post('/chirps', [
            'content' => ''
        ]);

        $reponse->assertSessionHasErrors(['content']);
    }

    public function test_un_chirp_ne_peut_pas_depasse_255_caracteres()
    {
        $utilisateur = User::factory()->create();
        $this->actingAs($utilisateur);

        $reponse = $this->post('/chirps', [
            'content' => str_repeat('a', 256)
        ]);

        $reponse->assertSessionHasErrors(['content']);
    }

    public function test_les_chirps_sont_affiches_sur_la_page_d_accueil()
    {
        $chirps = Chirp::factory()->count(3)->create();

        $reponse = $this->get('/');

        foreach ($chirps as $chirp) {
            $reponse->assertSee($chirp->content);
        }
    }

    public function test_un_utilisateur_peut_modifier_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);

        $reponse = $this->put("/chirps/{$chirp->id}", [
            'content' => 'Chirp modifié'
        ]);

        $reponse->assertStatus(200);
        $this->assertDatabaseHas('chirps', [
            'id' => $chirp->id,
            'content' => 'Chirp modifié',
        ]);
    }

    public function test_un_utilisateur_peut_supprimer_son_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);

        $reponse = $this->delete("/chirps/{$chirp->id}");

        $reponse->assertStatus(200);
        $this->assertDatabaseMissing('chirps', [
            'id' => $chirp->id,
        ]);
    }
}