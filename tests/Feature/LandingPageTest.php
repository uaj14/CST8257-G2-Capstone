<?php

use App\Models\User;

it('presents Multitasker to guests with clear authentication actions', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Multitasker')
        ->assertSee('Turn scattered tasks into a clear plan.')
        ->assertSee('Get started')
        ->assertSee('Log in')
        ->assertDontSee('Laravel has an incredibly rich ecosystem.');
});

it('presents authenticated visitors with a dashboard action', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('Open dashboard')
        ->assertDontSee('Create your account');
});

it('uses the Multitasker name in page titles', function () {
    config(['app.name' => 'Multitasker']);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<title>', false)
        ->assertSee('Log in - Multitasker');
});
