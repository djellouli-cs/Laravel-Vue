<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Numero;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Affiche la liste des posts avec leur numero associé.
     */
    public function index()
    {
        return Inertia::render('Numero/MultiCrud/ManagerPost', [
            'classes' => Post::with('numero')->get(),   // Inclure la relation numero
            'numeros' => Numero::all(),                 // Pour le menu déroulant <select>
        ]);
    }

    /**
     * Stocke un nouveau post dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'post' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'numero_id' => 'required|exists:numeros,id',
        ]);

        Post::create([
            'post' => $request->post,
            'marque' => $request->marque,
            'numero_id' => $request->numero_id,
        ]);

        return redirect()->back()->with('success', 'Post created successfully.');
    }

    /**
     * Met à jour un post existant.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'post' => 'required|string|max:255',
            'marque' => 'required|string|max:255',
            'numero_id' => 'required|exists:numeros,id',
        ]);

        $post = Post::findOrFail($id);
        $post->update([
            'post' => $request->post,
            'marque' => $request->marque,
            'numero_id' => $request->numero_id,
        ]);

        return redirect()->back()->with('success', 'Post updated successfully.');
    }

    /**
     * Supprime un post.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
}
