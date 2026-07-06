<?php

namespace App\Http\Controllers;

use App\Models\Uploads;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class UploadsController extends Controller
{
    /**
     * Upload Data (Protected Access).
     */
    public function upload(Request $request)
    {

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'upload_date' => 'required|date',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
        ]);

        // Save the upload
        $uploads = Uploads::create([
            'title' => $request->title,
            'description' => $request->description,
            'upload_date' => $request->upload_date,
            'event_date' => $request->event_date,
            'location' => $request->location,
        ]);

        return response()->json([
            'message' => 'Upload successful', 
            'data' => $uploads // Result is sent in the "data" body for use like "let uploadId = result.data.id;" to retrieve the ID of the uploaded content
        ], 201);
    }

    /**
     * Retrieve all uploads (Public Access).
     */
    public function retrieve()
    {
        $uploads = Uploads::all();
        
        return response()->json([
            'success' => true,
            'data' => $uploads
        ], 200);
    }

    /**
     * Delete all uploads (Public Access).
     */
    public function delete($id)
    {
        try {
            // Use the correct model name "Uploads"
            $upload = Uploads::findOrFail($id);
   
            // Delete the upload
            $upload->delete();
    
            return response()->json(['message' => 'Upload deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Upload not found.'], 404);
        }
    }

}
