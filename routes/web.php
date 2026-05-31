<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\NewsVoteController;
use App\Http\Controllers\SearchController;

// ==================== PUBLIC ROUTES ====================
Route::get('/', [FeedController::class, 'index'])->name('feed');
Route::get('/news', [NewsController::class, 'index'])->name('news');

// ✅ PROFILE ROUTES (HANYA SATU DEFINISI - HAPUS YANG LAMA)
// Gunakan parameter {profile} agar konsisten dengan model binding / manual fetch
Route::get('/profile/{profile}', [ProfileController::class, 'show'])->name('profile');
Route::get('/profile/{profile}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/{profile}', [ProfileController::class, 'update'])->name('profile.update');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== PROTECTED ROUTES ====================
// ⚠️ TANPA CLOSURE MIDDLEWARE - Auth check sudah ada di dalam controller methods

// Post CRUD (Feed)
Route::get('/post/create', [PostController::class, 'create'])->name('post.create');
Route::post('/post/store', [PostController::class, 'store'])->name('post.store');
Route::get('/post/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');

// Comment & Reaction (AJAX)
Route::post('/post/{post}/comment', [CommentController::class, 'store'])->name('comment.store');
Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
Route::post('/post/{post}/reaction', [ReactionController::class, 'toggle'])->name('reaction.toggle');

// News CRUD (Protected)
Route::get('/news/create', [NewsController::class, 'create'])->name('news.create');
Route::post('/news/store', [NewsController::class, 'store'])->name('news.store');
Route::get('/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
Route::put('/news/{news}', [NewsController::class, 'update'])->name('news.update');
Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');

// News Vote (AJAX)
Route::post('/news/{news}/vote', [NewsVoteController::class, 'toggle'])->name('news.vote');

// Profile Routes
Route::get('/profile/{profile}', [ProfileController::class, 'show'])->name('profile');
Route::get('/profile/{profile}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/{profile}', [ProfileController::class, 'update'])->name('profile.update');

// ==================== SEARCH ROUTES ====================
Route::get('/search/feed', [SearchController::class, 'feed'])->name('search.feed');
Route::get('/search/news', [SearchController::class, 'news'])->name('search.news');