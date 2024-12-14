<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chirp;

class ChirpController extends Controller
{
    public function index()
    {
        return view('chirps.index', [
            'chirps' => Chirp::latest()->get(),
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|max:255',
        ]);
    
        $request->user()->chirps()->create($validated);
    
        return response()->json(['message' => 'Chirp créé avec succès'], 201);
    }

    public function update(Request $request, Chirp $chirp)
    {
    // Vérifie que l'utilisateur a le droit de modifier le chirp
    $this->authorize('update', $chirp);

    $validated = $request->validate([
        'content' => 'required|max:255',
    ]);

    $chirp->update($validated);

    return response()->json(['message' => 'Chirp modifié avec succès'], 200);
    }

    public function destroy(Chirp $chirp)
    {
    // Vérifie que l'utilisateur a le droit de supprimer le chirp
    $this->authorize('delete', $chirp);

    $chirp->delete();

    return response()->json(['message' => 'Chirp supprimé avec succès'], 200);
    }
    }
