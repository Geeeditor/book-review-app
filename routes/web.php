<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookCOntroller;
use App\Http\Controllers\ReviewController;

Route::get('/', function () {
    return redirect()->route('books.index');
});

// only(['index', 'show']);-> specifies the only resource used on the controll
Route::resource('books', BookCOntroller::class)->only(['index', 'show'])
;


Route::resource('books.reviews', ReviewController::class)->scoped(['review' => 'book'])->only(['create', 'store']);
