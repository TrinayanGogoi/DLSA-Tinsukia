<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Links;

class LinksController extends Controller
{
     /**
     * Store a new link (Protected Access).
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'uploads_id' => 'required|exists:uploads,id',
            'link_title' => 'nullable|string|max:255',
            'link_url' => 'nullable|url',
            'link_location' => 'nullable|string|max:255',
        ]);

        // Create a new link entry
        $links = Links::create($request->all()); // Links is the model name not table name

        return response()->json([
            'message' => 'Link successfully added',
            'data' => $links
        ], 201);
    }

    /**
     * Retrieve all links (Public Access).
     */
    public function retrieve()
    {
        $links = Links::all();

        return response()->json([
            'success' => true,
            'data' => $links
        ], 200);
    }
}
