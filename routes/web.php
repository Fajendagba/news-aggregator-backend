<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    //return view('welcome');
    return response()->json([
        "message"=>"Server is up and running"]);
});