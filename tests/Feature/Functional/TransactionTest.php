<?php


it('returns unauthorized for unauthenticated users', function () {
    // post request to the endpoint
    $response = $this->postJson('/api/v1/transactions/list', [
        'wallet_no' => '1234567890'
    ]);
    $response->assertUnauthorized();
});

it('returns bad request for invalid data', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user, 'api')->postJson('/api/v1/transactions/list', [
        'wallet_no' => '1234567890'
    ]);
    $response->assertStatus(422);
});

it('returns not found for non-existent wallet', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user, 'api')->postJson('/api/v1/transactions/list', [
        'wallet_no' => '12345678900'
    ]);
    $response->assertStatus(404)
        ->assertJson([
            'error' => 'Wallet not found',
            'message' => 'Wallet not found for the user'
        ]);
});
