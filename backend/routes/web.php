<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status' => 200,
        'message' => 'OK',
        'data' => "Why dot net developers don't wear glasses? Because they see sharp",
    ]);
});
