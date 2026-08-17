<?php

use App\Livewire\Task\Show;
use App\Models\TaskList;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('task-lists/{taskList}/tasks', function (TaskList $taskList) {
        Gate::authorize('view', $taskList);

        return view('tasks.index', ['taskList' => $taskList]);
    })->name('tasks.index');

    Route::get('task-lists/{taskList}/tasks/{task}', Show::class)
        ->name('tasks.show')
        ->middleware('auth');

    Route::get('trash', function () {
        return view('tasks.trash');
    })->name('tasks.trash');
});

require __DIR__.'/settings.php';
