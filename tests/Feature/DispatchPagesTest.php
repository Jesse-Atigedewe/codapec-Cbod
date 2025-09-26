<?php

use App\Models\User;
use App\Models\Dispatch;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('returns dispatch index', function () {
    $user = User::first() ?: User::factory()->create();

    actingAs($user);

    get('/dispatches')->assertStatus(200);
});

it('returns dispatch info when a dispatch exists', function () {
    $user = User::first() ?: User::factory()->create();

    actingAs($user);

    $dispatch = Dispatch::first();
    if (! $dispatch) {
        $this->markTestSkipped('No dispatch exists to test info page.');
        return;
    }

    get("/dispatches/{$dispatch->id}/info")->assertStatus(200);
});
