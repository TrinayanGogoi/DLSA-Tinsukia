<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Latest;

class LatestController extends Controller
{
    /**
     * Store a new latest entry (Protected Access).
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'uploads_id' => 'required|exists:uploads,id',
            'expires_at' => 'nullable|date',
        ]);

        // Create a new latest entry
        $latest = Latest::create($request->all());

        return response()->json([
            'message' => 'Latest entry successfully added',
            'data' => $latest
        ], 201);
    }

    /**
     * Retrieve all latest entries (Public Access).
     */
    public function retrieve()
    {
        $latest = Latest::with(['upload' => function($query) {
            $query->with(['pictures', 'pdfs', 'links']);
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $latest
        ], 200);
    }

    /**
     * Delete a specific latest entry (Protected Access).
     */
    public function delete($id)
    {
        $latest = Latest::find($id);
        if ($latest) {
            $latest->delete();
            return response()->json([
                'message' => 'Latest entry successfully deleted',
            ], 200);
        }
        return response()->json([
            'message' => 'Latest entry not found',
        ], 404);
    }

    /**
     * Update expiry date of a latest entry (Protected Access).
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'expires_at' => 'required|date',
        ]);

        $latest = Latest::find($id);
        if ($latest) {
            $latest->update([
                'expires_at' => $request->expires_at
            ]);
            return response()->json([
                'message' => 'Latest entry successfully updated',
                'data' => $latest
            ], 200);
        }
        return response()->json([
            'message' => 'Latest entry not found',
        ], 404);
    }
}
