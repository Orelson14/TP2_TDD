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

    public function test_nombre_maximum_de_chirps()
    {
        $utilisateur = User::factory()->create();
        Chirp::factory()->count(10)->create(['user_id' => $utilisateur->id]);
        $this->actingAs($utilisateur);

        $reponse = $this->post('/chirps', [
            'content' => 'Chirp supplémentaire'
        ]);

        $reponse->assertStatus(403);
    }

    public function test_affichage_des_chirps_recents()
    {
        Chirp::factory()->create(['created_at' => now()->subDays(8)]);
        $chirpRecent = Chirp::factory()->create(['created_at' => now()]);
        $reponse = $this->get('/');

        $reponse->assertSee($chirpRecent->content);
        $reponse->assertDontSee('Chirp trop ancien');
    }

    public function test_un_utilisateur_ne_peut_pas_modifier_le_chirp_d_un_autre()
    {
        $utilisateur1 = User::factory()->create();
        $utilisateur2 = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur1->id]);

        $this->actingAs($utilisateur2);

        $reponse = $this->put("/chirps/{$chirp->id}", [
            'content' => 'Modification non autorisée'
        ]);

        $reponse->assertStatus(403);
    }

    public function test_un_utilisateur_ne_peut_pas_supprimer_le_chirp_d_un_autre()
    {
        $utilisateur1 = User::factory()->create();
        $utilisateur2 = User::factory()->create();
        $chirp = Chirp::factory()->create(['user_id' => $utilisateur1->id]);

        $this->actingAs($utilisateur2);

        $reponse = $this->delete("/chirps/{$chirp->id}");

        $reponse->assertStatus(403);
    }

    public function test_utilisateur_peut_liker_un_chirp()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create();
        $this->actingAs($utilisateur);

        $reponse = $this->post("/chirps/{$chirp->id}/like");
        $reponse->assertStatus(201);

        $this->assertDatabaseHas('likes', [
            'chirp_id' => $chirp->id,
            'user_id' => $utilisateur->id,
        ]);
    }

    public function test_utilisateur_ne_peut_pas_liker_deux_fois()
    {
        $utilisateur = User::factory()->create();
        $chirp = Chirp::factory()->create();
        $this->actingAs($utilisateur);

        $this->post("/chirps/{$chirp->id}/like");
        $reponse = $this->post("/chirps/{$chirp->id}/like");
        $reponse->assertStatus(403);
    }
}
