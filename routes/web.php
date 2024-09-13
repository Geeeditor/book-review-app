<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookCOntroller;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('books', BookCOntroller::class);
