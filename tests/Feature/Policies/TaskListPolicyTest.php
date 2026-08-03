<?php

use App\Models\TaskList;
use App\Models\User;

test('an authenticated user can view task lists', function () {
    $user = User::factory()->create();

    expect($user->can('viewAny', TaskList::class))->toBeTrue();
});

test('an authenticated user can create a task list', function () {
    $user = User::factory()->create();

    expect($user->can('create', TaskList::class))->toBeTrue();
});

test('the owner can view their task list', function () {
    $user = User::factory()->create();

    $taskList = TaskList::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('view', $taskList))->toBeTrue();
});

test('another user cannot view a task list', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $taskList = TaskList::factory()->create([
        'user_id' => $owner->id,
    ]);

    expect($otherUser->can('view', $taskList))->toBeFalse();
});

test('the owner can update their task list', function () {
    $user = User::factory()->create();

    $taskList = TaskList::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('update', $taskList))->toBeTrue();
});

test('another user cannot update a task list', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $taskList = TaskList::factory()->create([
        'user_id' => $owner->id,
    ]);

    expect($otherUser->can('update', $taskList))->toBeFalse();
});

test('the owner can delete their task list', function () {
    $user = User::factory()->create();

    $taskList = TaskList::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('delete', $taskList))->toBeTrue();
});

test('another user cannot delete a task list', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();

    $taskList = TaskList::factory()->create([
        'user_id' => $owner->id,
    ]);

    expect($otherUser->can('delete', $taskList))->toBeFalse();
});

test('task lists cannot be restored or permanently deleted', function () {
    $user = User::factory()->create();

    $taskList = TaskList::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->can('restore', $taskList))->toBeFalse()
        ->and($user->can('forceDelete', $taskList))->toBeFalse();
});