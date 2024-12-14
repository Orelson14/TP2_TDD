<?php

namespace App\Policies;

use App\Models\Chirp;
use App\Models\User;

class ChirpPolicy
{
    /**
     * Détermine si l'utilisateur peut modifier le chirp.
     */
    public function update(User $user, Chirp $chirp)
    {
        return $user->id === $chirp->user_id;
    }

    /**
     * Détermine si l'utilisateur peut supprimer le chirp.
     */
    public function delete(User $user, Chirp $chirp)
    {
        return $user->id === $chirp->user_id;
    }
}
