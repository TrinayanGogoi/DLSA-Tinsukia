<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider_Picture;
use Illuminate\Support\Facades\Storage;

class SliderPictureController extends Controller
{
    /**
     * Store a new slider picture (Protected Access).
     */
    public function upload(Request $request)
    {
        // Validate the request
        $request->validate([
            'slider_picture_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Ensure it's an image file, max 5MB
            'slider_picture_title' => 'nullable|string|max:255',
        ]);

        // Store the uploaded image in storage/app/public/slider_picture
        $path = $request->file('slider_picture_path')->store('slider_picture', 'public');

        // Create a new slider picture entry in the database
        $sliderPicture = Slider_Picture::create([
            'slider_picture_path' => $path,
            'slider_picture_title' => $request->slider_picture_title,
        ]);

        return response()->json([
            'message' => 'Slider picture successfully added',
            'data' => $sliderPicture
        ], 201);
    }

    /**
     * Retrieve all slider pictures (Public Access).
     */
    public function retrieve()
    {
        $sliderPictures = Slider_Picture::all();

        return response()->json([
            'success' => true,
            'data' => $sliderPictures
        ], 200);
    }

    /**
     * Update an existing slider picture (Protected Access).
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'slider_picture_path' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Ensure it's an image file, max 5MB
            'slider_picture_title' => 'nullable|string|max:255',
        ]);

        // Find the slider picture and delete old image
        $oldSliderPicture = Slider_Picture::find($id);
        if ($oldSliderPicture && $oldSliderPicture->slider_picture_path) {
            Storage::disk('public')->delete($oldSliderPicture->slider_picture_path);
        }

        // Store the uploaded image in storage/app/public/slider_picture
        $path = $request->file('slider_picture_path')->store('slider_picture', 'public');

        // Update the slider picture in the database
        $sliderPicture = Slider_Picture::find($id)->update([
            'slider_picture_path' => $path,
            'slider_picture_title' => $request->slider_picture_title,
        ]);

        return response()->json([
            'message' => 'Slider picture successfully updated',
            'data' => $sliderPicture
        ], 200);
    }

    /**
     * Delete a slider picture (Protected Access).
     */
    public function delete($id)
    {
        $sliderPicture = Slider_Picture::find($id);
        if ($sliderPicture) {
            // Delete the file if path exists
            if ($sliderPicture->slider_picture_path) {
                Storage::disk('public')->delete($sliderPicture->slider_picture_path);
            }
            
            // Update the record to clear the path and title instead of deleting
            $sliderPicture->update([
                'slider_picture_path' => null,
                'slider_picture_title' => null
            ]);

            return response()->json([
                'message' => 'Slider picture successfully cleared',
            ], 200);
        }
        
        return response()->json([
            'message' => 'Slider picture not found',
        ], 404);
    }
    
}
