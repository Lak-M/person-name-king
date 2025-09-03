<?php

declare(strict_types=1);

use App\Http\Controllers\FormatController;
use Illuminate\Support\Facades\Route;

Route::post('full-name/format', [FormatController::class, 'formatFullName'])->name('format-full-name');
Route::post('name-parts/format', [FormatController::class, 'formatNameParts'])->name('format-name-parts');
