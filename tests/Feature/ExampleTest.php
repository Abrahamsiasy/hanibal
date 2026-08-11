<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the root url shows city picker for guests', function () {
    $this->get('/')->assertSuccessful();
});
