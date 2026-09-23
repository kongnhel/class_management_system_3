<?php

use App\Models\ProfessorTrustedDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('repeated trust requests from the same browser do not create duplicate devices', function () {
    $professor = User::factory()->professor()->create();

    $firstResponse = $this->actingAs($professor)
        ->post(route('professor.security.trusted-device.store'));

    $this->actingAs($professor)
        ->withCookie('professor_trusted_device', $firstResponse->getCookie('professor_trusted_device')->getValue())
        ->post(route('professor.security.trusted-device.store'))
        ->assertSessionHas('success', 'This device is already trusted.');

    expect(ProfessorTrustedDevice::where('user_id', $professor->id)->count())->toBe(1);
});
