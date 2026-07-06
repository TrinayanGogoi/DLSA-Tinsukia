<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pictures;

class PicturesController extends Controller
{
    /**
     * Store a new picture (Protected Access).
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'uploads_id' => 'required|exists:uploads,id',
            'picture_path' => 'required|file|mimes:jpg,jpeg,png|max:2048', // Ensure it's a file
            'picture_title' => 'nullable|string|max:255',
        ]);

         // Store the uploaded picture in storage/app/public/pictures
        $path = $request->file('picture_path')->store('pictures', 'public');

        // Create a new picture entry in the database
        $pictures = Pictures::create([
            'uploads_id' => $request->uploads_id,
            'picture_path' => $path, // Save the stored file path
            'picture_title' => $request->picture_title,
        ]);

        return response()->json([
            'message' => 'Picture successfully added',
            'data' => $pictures
        ], 201);
    }

    /**
     * Retrieve all pictures (Public Access).
     */
    public function retrieve()
    {
        $pictures = Pictures::all();

        return response()->json([
            'success' => true,
            'data' => $pictures
        ], 200);
    }
}
