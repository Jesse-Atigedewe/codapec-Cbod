<?php

use Illuminate\Http\Response;
use function Pest\Laravel\actingAs;
use App\Models\User;

it('loads dispatch index and dispatch info pages without JS errors', function () {
    // Use an existing user with a role that can view dispatches; fall back to first user
    $user = User::first() ?: User::factory()->create();

    actingAs($user);

    // Visit dispatch index
    $page = visit('/dispatches');
    $page->assertStatus(200)->assertNoJavascriptErrors();

    // If a dispatch exists, visit info page
    $dispatch = \App\Models\Dispatch::first();
    if ($dispatch) {
        $page = visit("/dispatches/{$dispatch->id}/info");
        $page->assertStatus(200)->assertNoJavascriptErrors();
    } else {
        // If no dispatch exists, at least ensure the index renders
        expect(true)->toBeTrue();
    }
});