<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChirpController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|max:255',
        ]);
    
        $request->user()->chirps()->create($validated);
    
        return response()->json(['message' => 'Chirp créé avec succès'], 201);
    }
}
