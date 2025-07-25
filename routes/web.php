<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');



Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('upload', function () {
    return Inertia::render('Upload');
})->middleware(['auth', 'verified'])->name('upload');

Route::get('files', function () {
    return Inertia::render('Files');
})->middleware(['auth', 'verified'])->name('files');

Route::get('permissions', function () {
    return Inertia::render('Permissions');
})->middleware(['auth', 'verified'])->name('permissions');

// Routes API pour l'upload
Route::post('api/upload', [App\Http\Controllers\FileUploadController::class, 'upload'])
    ->middleware(['auth', 'verified'])->name('api.upload');
Route::get('api/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
})->middleware(['auth', 'verified'])->name('api.csrf-token');
Route::get('api/files', [App\Http\Controllers\FileUploadController::class, 'getFiles'])
    ->middleware(['auth', 'verified'])->name('api.files');
Route::post('api/folders', [App\Http\Controllers\FileUploadController::class, 'createFolder'])
    ->middleware(['auth', 'verified'])->name('api.folders.create');
Route::delete('api/files', [App\Http\Controllers\FileUploadController::class, 'deleteItems'])
    ->middleware(['auth', 'verified'])->name('api.files.delete');
Route::post('api/files/{file}/assign', [App\Http\Controllers\FileUploadController::class, 'assignFile'])
    ->middleware(['auth', 'verified'])->name('api.files.assign');

// Routes API pour la gestion des permissions
Route::get('api/users', [App\Http\Controllers\PermissionController::class, 'getUsers'])
    ->middleware(['auth', 'verified'])->name('api.users.index');
Route::post('api/users', [App\Http\Controllers\PermissionController::class, 'createUser'])
    ->middleware(['auth', 'verified'])->name('api.users.create');
Route::put('api/users/{user}', [App\Http\Controllers\PermissionController::class, 'updateUser'])
    ->middleware(['auth', 'verified'])->name('api.users.update');
Route::delete('api/users/{user}', [App\Http\Controllers\PermissionController::class, 'deleteUser'])
    ->middleware(['auth', 'verified'])->name('api.users.delete');
Route::patch('api/users/{user}/status', [App\Http\Controllers\PermissionController::class, 'toggleUserStatus'])
    ->middleware(['auth', 'verified'])->name('api.users.status');

Route::get('api/roles', [App\Http\Controllers\PermissionController::class, 'getRoles'])
    ->middleware(['auth', 'verified'])->name('api.roles.index');
Route::post('api/roles', [App\Http\Controllers\PermissionController::class, 'createRole'])
    ->middleware(['auth', 'verified'])->name('api.roles.create');

Route::get('api/groups', [App\Http\Controllers\PermissionController::class, 'getGroups'])
    ->middleware(['auth', 'verified'])->name('api.groups.index');
Route::post('api/groups', [App\Http\Controllers\PermissionController::class, 'createGroup'])
    ->middleware(['auth', 'verified'])->name('api.groups.create');

Route::get('api/permissions', [App\Http\Controllers\PermissionController::class, 'getPermissions'])
    ->middleware(['auth', 'verified'])->name('api.permissions.index');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
