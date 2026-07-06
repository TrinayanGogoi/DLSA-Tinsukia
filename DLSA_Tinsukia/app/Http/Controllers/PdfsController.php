<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pdfs;

class PdfsController extends Controller
{
    /**
     * Store a new PDF (Protected Access).
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'uploads_id' => 'required|exists:uploads,id',
            'pdf_path' => 'required|file|mimes:pdf|max:10240', // Ensure it's a PDF file, max 10MB
            'pdf_title' => 'nullable|string|max:255',
        ]);

         // Store the uploaded PDF in storage/app/public/pdfs
        $path = $request->file('pdf_path')->store('pdfs', 'public');

        // Create a new PDF entry in the database
        $pdf = Pdfs::create([
            'uploads_id' => $request->uploads_id,
            'pdf_path' => $path, // Save the stored file path
            'pdf_title' => $request->pdf_title,
        ]);

        return response()->json([
            'message' => 'PDF successfully added',
            'data' => $pdf
        ], 201);
    }

    /**
     * Retrieve all PDFs (Public Access).
     */
    public function retrieve()
    {
        $pdfs = Pdfs::all();

        return response()->json([
            'success' => true,
            'data' => $pdfs
        ], 200);
    }
}
