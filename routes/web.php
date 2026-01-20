<?php

use App\Http\Controllers\AnimeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AnimeController::class, 'index'])->name('home');
Route::get('/series', [AnimeController::class, 'directory'])->name('directory');
Route::get('/search', [AnimeController::class, 'search'])->name('anime.search');
Route::get('/series/{slug}', [AnimeController::class, 'show'])->name('anime.detail');
Route::get('/series/{animeSlug}/episode/{episodeSlug}', [AnimeController::class, 'watch'])->name('anime.watch');
