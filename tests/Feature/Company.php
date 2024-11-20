<?php

test('company_list', function () {
    $response = $this->get('/api/company');

    $response->assertStatus(200);
});
