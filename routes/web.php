<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\ProjectController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\ContactQueryController;
use App\Http\Controllers\Admin\ProfileController;

// ─── Frontend ────────────────────────────────────────────────────────────────
Route::middleware(['track.pageview'])->group(function () {
    Route::get('/',               [HomeController::class,    'index'])->name('home');
    Route::get('/about',          [AboutController::class,   'index'])->name('about');
    Route::get('/projects',       [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/services',       [ServiceController::class, 'index'])->name('services');
    Route::get('/blogs',          [BlogController::class,    'index'])->name('blogs.index');
    Route::get('/blogs/{slug}',   [BlogController::class,    'show'])->name('blogs.show');
    Route::post('/blogs/{slug}/comment', [BlogController::class, 'comment'])->name('blogs.comment');
    Route::get('/contact',        [ContactController::class, 'index'])->name('contact');
    Route::post('/contact',       [ContactController::class, 'store'])->name('contact.store');
});

// ─── Admin Auth ───────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest only
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
        Route::get('/forgot-password',  [ForgotPasswordController::class, 'show'])->name('forgot-password');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('forgot-password.send');
        Route::get('/reset-password/{token}', [PasswordResetController::class, 'show'])->name('reset-password');
        Route::post('/reset-password',        [PasswordResetController::class, 'reset'])->name('reset-password.submit');
    });

    // Authenticated but awaiting 2FA
    Route::middleware('admin.auth')->group(function () {
        Route::get('/two-factor',        [TwoFactorController::class, 'show'])->name('two-factor');
        Route::post('/two-factor',       [TwoFactorController::class, 'verify'])->name('two-factor.verify');
        Route::post('/two-factor/resend', [TwoFactorController::class, 'resend'])->name('two-factor.resend');
    });

    // Fully authenticated
    Route::middleware(['admin.auth', 'two-factor.verified'])->group(function () {
        Route::get('/dashboard',       [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        Route::resource('projects', AdminProjectController::class);
        Route::post('/projects/{id}/toggle-featured', [AdminProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
        Route::post('/projects/{id}/toggle-status',   [AdminProjectController::class, 'toggleStatus'])->name('projects.toggle-status');
        Route::delete('/projects/{id}/image/{imageId}', [AdminProjectController::class, 'deleteImage'])->name('projects.delete-image');

        Route::resource('blogs', AdminBlogController::class);
        Route::post('/blogs/{id}/toggle-featured', [AdminBlogController::class, 'toggleFeatured'])->name('blogs.toggle-featured');
        Route::post('/blogs/{id}/toggle-status',   [AdminBlogController::class, 'toggleStatus'])->name('blogs.toggle-status');
        Route::post('/blogs/{id}/comments/{commentId}/approve', [AdminBlogController::class, 'approveComment'])->name('blogs.approve-comment');
        Route::delete('/blogs/{id}/comments/{commentId}',       [AdminBlogController::class, 'deleteComment'])->name('blogs.delete-comment');

        Route::get('/contacts',              [ContactQueryController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{id}',         [ContactQueryController::class, 'show'])->name('contacts.show');
        Route::post('/contacts/{id}/reply',  [ContactQueryController::class, 'reply'])->name('contacts.reply');
        Route::post('/contacts/{id}/status', [ContactQueryController::class, 'updateStatus'])->name('contacts.update-status');
        Route::delete('/contacts/{id}',      [ContactQueryController::class, 'destroy'])->name('contacts.destroy');
        Route::post('/contacts/bulk-action', [ContactQueryController::class, 'bulkAction'])->name('contacts.bulk-action');

        Route::get('/profile',                         [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/profile/edit',                    [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile',                         [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/security',                [ProfileController::class, 'security'])->name('profile.security');
        Route::put('/profile/password',                [ProfileController::class, 'updatePassword'])->name('profile.update-password');
        Route::post('/profile/two-factor/enable',      [ProfileController::class, 'enableTwoFactor'])->name('profile.enable-2fa');
        Route::post('/profile/two-factor/disable',     [ProfileController::class, 'disableTwoFactor'])->name('profile.disable-2fa');
        Route::post('/profile/two-factor/verify-setup', [ProfileController::class, 'verifyTwoFactorSetup'])->name('profile.verify-2fa-setup');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    });
});
