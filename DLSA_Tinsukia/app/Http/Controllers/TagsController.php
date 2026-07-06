<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tags;
use App\Models\Uploads;

class TagsController extends Controller
{
    /**
     * Store Tags Data (Protected Access).
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'uploads_id' => 'required|exists:uploads,id',
            'achievement' => 'boolean',
            'activity_calendar' => 'boolean',
            'advertisement' => 'boolean',
            'awareness_meeting' => 'boolean',
            'awareness_program' => 'boolean',
            'juvenile_justice' => 'boolean',
            'legal_aid' => 'boolean',
            'legal_assistance' => 'boolean',
            'lok_adalat' => 'boolean',
            'legal_literacy_classes' => 'boolean',
            'mediation' => 'boolean',
            'monitoring_legal_clinic' => 'boolean',
            'monitoring_jail' => 'boolean',
            'meeting' => 'boolean',
            'notice' => 'boolean',
            'observance' => 'boolean',
            'results' => 'boolean',
            'schemes' => 'boolean',
            'victim_compensation' => 'boolean',
            'workshop' => 'boolean',
            'recruitment' => 'boolean',
        ]);

        // Create a new tag entry
        $tags = Tags::create($request->all());

        return response()->json([
            'message' => 'Tags saved successfully', 
            'data' => $tags
        ], 201);
    }

    /**
     * Retrieve all tags (Public Access).
     */
    public function retrieve()
    {
        $tags = Tags::all();

        return response()->json([
            'success' => true,
            'data' => $tags
        ], 200);
    }
}
