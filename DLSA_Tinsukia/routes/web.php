<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
     // Retrieve the token from the session
    $admin = Auth::guard('api')->user();
    $token = session('authToken');
    return view('dashboard', ['token' => $token], ['admin' => $admin]);
});

Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/ContentUpload', function () {
    // Retrieve the token from the session
   $admin = Auth::guard('api')->user();
   $token = session('authToken');
   return view('ContentUpload', ['token' => $token], ['admin' => $admin]);
});

Route::get('/DisplayContent', function () {
    // Retrieve the token from the session
   $admin = Auth::guard('api')->user();
   $token = session('authToken');
   return view('DisplayContent', ['token' => $token], ['admin' => $admin]);
});

Route::get('/SliderPicture', function () {
    // Retrieve the token from the session
   $admin = Auth::guard('api')->user();
   $token = session('authToken');
   return view('SliderPicture', ['token' => $token], ['admin' => $admin]);
});

Route::get('/Latest', function () {
    // Retrieve the token from the session
   $admin = Auth::guard('api')->user();
   $token = session('authToken');
   return view('Latest', ['token' => $token], ['admin' => $admin]);
});

// Route::middleware('auth:api')->get('/dashboard', function (Request $request) {
//     $admin = Auth::guard('api')->user(); // Get authenticated admin
//     $token = $request->bearerToken(); // Retrieve the token from the request

//     if (!$admin || !$token) {
//         return Response::json(['error' => 'Unauthorized: Invalid Token.'], 401);
//     }

//     return Response::json([
//         'message' => 'Welcome to the dashboard!',
//         'admin' => $admin,
//         'token' => $token, // Include the token in the response
//     ]);
// });

// Route::get('/dashboard', function (Request $request) {
//     $admin = $request->user(); // Get authenticated admin
//     $token = $request->bearerToken(); // Retrieve the token from the request

//     if (!$admin || !$token) {
//         return Response::json(['error' => 'Unauthorized: Invalid Token.'], 401);
//     }

//     return Response::json([
//         'message' => 'Welcome to the dashboard!',
//         'admin' => $admin,
//         'token' => $token, // Include the token in the response
//     ]);
// });

