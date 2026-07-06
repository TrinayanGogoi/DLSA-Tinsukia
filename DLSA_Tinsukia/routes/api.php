<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UploadsController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\LinksController;
use App\Http\Controllers\PicturesController;
use App\Http\Controllers\PdfsController;
use App\Http\Controllers\SliderPictureController;
use App\Http\Controllers\LatestController;

// ADMIN Table
Route::post('/register', [AuthController::class, 'register']); // not protected route
Route::post('/login', [AuthController::class, 'login']); // not protected route, Sends 2FA code
Route::post('/verify-2fa', [AuthController::class, 'verify2FA']); // Verifies 2FA
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']); // protected route

// Get authenticated admin details
Route::get('/admin', function (Request $request) {
    return $request->user(); // This returns the logged-in admin
})->middleware('auth:api'); // protected route


// UPLOADS Table
Route::middleware('auth:api')->post('/uploads/Upload', [UploadsController::class, 'upload']);// Protect the upload route so only admins with a valid Bearer token can access
Route::get('/uploads/Retrieve', [UploadsController::class, 'retrieve']); // Public route to retrieve all uploads
Route::delete('/uploads/Delete/{id}', [UploadsController::class, 'delete']); // Public route to delete perticular upload

// TAGS Table
Route::middleware('auth:api')->post('/tags/Upload', [TagsController::class, 'upload']); // Protect the create tags route with authentication
Route::get('/tags/Retrieve', [TagsController::class, 'retrieve']); // Get all tags (Public)

// LINKS Table
Route::middleware('auth:api')->post('/links/Upload', [LinksController::class, 'upload']); // Store a new link (Protected)
Route::get('/links/Retrieve', [LinksController::class, 'retrieve']); // Get all links (Public)

// PICTURES Table
Route::middleware('auth:api')->post('/pictures/Upload', [PicturesController::class, 'upload']); // Store a new picture (Protected)
Route::get('/pictures/Retrieve', [PicturesController::class, 'retrieve']); // Get all pictures (Public)

// PDFs Table
Route::middleware('auth:api')->post('/pdfs/Upload', [PdfsController::class, 'upload']); // Store a new pdf (Protected)
Route::get('/pdfs/Retrieve', [PdfsController::class, 'retrieve']); // Get all pdf (Public)

// SLIDER_PICTURE Table
Route::middleware('auth:api')->post('/slider_picture/Upload', [SliderPictureController::class, 'upload']); // Store a new slider picture (Protected)
Route::get('/slider_picture/Retrieve', [SliderPictureController::class, 'retrieve']); // Get all slider pictures (Public)
Route::middleware('auth:api')->post('/slider_picture/Update/{id}', [SliderPictureController::class, 'update']); // Update an existing slider picture (Protected)
Route::middleware('auth:api')->delete('/slider_picture/Delete/{id}', [SliderPictureController::class, 'delete']); // Delete a slider picture (Protected)

// LATEST Table
Route::middleware('auth:api')->post('/latest/Upload', [LatestController::class, 'upload']); // Store a new latest entry (Protected)
Route::get('/latest/Retrieve', [LatestController::class, 'retrieve']); // Get all latest entries (Public)
Route::middleware('auth:api')->delete('/latest/Delete/{id}', [LatestController::class, 'delete']); // Delete a specific latest entry (Protected)
Route::post('/latest/Update/{id}', [LatestController::class, 'update'])->middleware('auth:api');
