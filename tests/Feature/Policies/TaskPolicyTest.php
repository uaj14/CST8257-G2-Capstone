<?php

use App\Models\Task;
use App\Models\User;

test('an authenticated user can view tasks', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', Task::class))->toBeTrue();
});

test('an authenticated user can create a task', function () {
    $user = User::factory()->create();

    expect($user->can('create', Task::class))->toBeTrue();
});

test('the owner can view their task', function () {
    $user = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('view', $task))->toBeTrue();
});

test('another user cannot view a task', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $owner->id,
    ]);

    expect($otherUser->can('view', $task))->toBeFalse();
});

test('the owner can update their task', function () {
    $user = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('update', $task))->toBeTrue();
});

test('another user cannot update a task', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $owner->id,
    ]);

    expect($otherUser->can('update', $task))->toBeFalse();
});

test('the owner can delete their task', function () {
    $user = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('delete', $task))->toBeTrue();
});

test('another user cannot delete a task', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $owner->id,
    ]);

    expect($otherUser->can('delete', $task))->toBeFalse();
});

test('tasks cannot be restored or permanently deleted', function () {
    $user = User::factory()->create();

    $task = Task::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('restore', $task))->toBeFalse()
        ->and($user->can('forceDelete', $task))->toBeFalse();
});
