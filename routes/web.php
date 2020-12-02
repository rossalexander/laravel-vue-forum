<?php

use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ThreadSubscriptionController;
use App\Http\Controllers\UserNotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Thread
Route::get('/threads', [ThreadController::class, 'index'])->name('threads');
Route::get('threads/create', [ThreadController::class, 'create']);
Route::get('/threads/{channel:slug}', [ThreadController::class, 'index']);
Route::get('/threads/{channel:slug}/{thread}', [ThreadController::class, 'show']);
Route::get('/threads/{channel}/{thread}/replies', [ReplyController::class, 'index']);
Route::post('/threads', [ThreadController::class, 'store']);
Route::post('/threads/{channel:slug}/{thread}/replies', [ReplyController::class, 'store']);
Route::post('/threads/{channel}/{thread}/subscriptions', [ThreadSubscriptionController::class, 'store'])->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions', [ThreadSubscriptionController::class, 'destroy'])->middleware('auth');
Route::delete('/threads/{channel:slug}/{thread}', [ThreadController::class, 'destroy']);

// Reply
Route::post('/replies/{reply}/favorites', [FavoriteController::class, 'store']);
Route::patch('/replies/{reply}', [ReplyController::class, 'update']);
Route::delete('/replies/{reply}', [ReplyController::class, 'destroy']);
Route::delete('/replies/{reply}/favorites', [FavoriteController::class, 'destroy']);

// Profile
Route::get('/profiles/{user:name}', [ProfileController::class, 'show'])->name('profile');
Route::get('/profiles/{user:name}/notifications', [UserNotificationController::class, 'index']);
Route::delete('/profiles/{user:name}/notifications/{notification}', [UserNotificationController::class, 'destroy']);
