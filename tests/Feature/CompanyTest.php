<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\{post, get, put, delete};

$token = null;

beforeEach(function () use (&$token) {
    $login = post(
        '/api/auth',
        ['email' => 'test@example.com', 'password' => 'test@example.com'],
        ['Accept' => 'application/json']
    );
    $token = $login['access_token'];
});

function createData($token, ?array $data = [])
{
    if (!$data) {
        $data = [
            'name' => 'test',
            'email' => 'test@test.com',
            'phone_number' => '0834234',
        ];
    }

    $response = post(
        '/api/company',
        $data,
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );

    return $response;
}

test('sample get company wrong role', function () {
    $login = post(
        '/api/auth',
        ['email' => 'manager@example.com', 'password' => 'manager@example.com'],
        ['Accept' => 'application/json']
    );

    $response = get(
        '/api/company',
        [
            'Authorization' => 'Bearer ' . $login['access_token'],
            'Accept' => 'application/json',
        ]
    );

    $response->assertStatus(403)->assertJson(['message' => 'User does not have the right roles.']);
});


test('sample get company whithout token', function () {
    $response = get(
        '/api/company',
        [
            'Accept' => 'application/json',
        ]
    );

    $response->assertStatus(401)->assertJson(
        fn(AssertableJson $json) =>
        $json->where('message', 'Unauthenticated.')
    );
});

test('view all company', function () use (&$token) {
    $response = get(
        '/api/company',
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );
    $response->assertStatus(200)->assertJsonStructure([
        'data' => [],
        'meta' => [],
        'links' => []
    ]);
});

test('view detail company with null data', function () use (&$token) {
    $response = get(
        '/api/company/999',
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );

    $response->assertStatus(200)->assertJsonStructure([
        'data' => [],
    ]);
});

test('view detail company with data auto create user manager', function () use (&$token) {
    createData($token);
    $model = DB::table('companies')->latest('id')->first();
    $response = get(
        "/api/company/$model->id",
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );

    $response->assertStatus(200)->assertJsonStructure(
        [
            'data' => [
                '*' => [
                    'name',
                    'email',
                    'phone_number',
                    'employe' => [
                        '*' => [
                            "position"
                        ]
                    ]
                ]
            ]
        ],
        [
            'data' => [
                '*' => [
                    'name' => 'test',
                    'email' => 'test@test.com',
                    'phone_number' => '0834234',
                    'employe' => [
                        '*' => [
                            "position" => "manager"
                        ]
                    ]
                ]
            ]
        ]
    );
});

test('create with null data company', function () use (&$token) {
    $response = post(
        '/api/company',
        [
            'name' => '',
            'email' => '',
            'phone_number' => '',
        ],
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );
    $response->assertStatus(422)->assertJsonStructure(
        [
            'errors' => [
                'name',
                'email',
                'phone_number',
            ]
        ],
        [
            "errors" => [
                "name" => ["The name field is required."],
                "email" => ["The email field is required."],
                "phone_number" => ["The phone number field is required."]
            ]
        ]

    );
});

test('create with wrong data company', function () use (&$token) {
    $response = createData(
        $token,
        [
            'name' => 'test',
            'email' => 'test',
            'phone_number' => 'test',
        ]
    );

    $response->assertStatus(422)->assertJsonStructure(
        [
            'errors' => [
                'email',
                'phone_number',
            ]
        ],
        [
            "errors" => [
                "email" => ["The email field must be a valid email address."],
                "phone_number" => ["The phone number field is required."]
            ]
        ]
    );
});

test('create with correct data company', function () use (&$token) {
    $response = createData($token);

    $response->assertStatus(200)->assertJson([
        'data' => [
            '0' => [
                'name' => 'test',
                'email' => 'test@test.com',
                'phone_number' => '0834234',
            ]
        ]
    ]);
});

test('update data company', function () use (&$token) {
    createData($token);
    $model = DB::table('companies')->latest('id')->first();
    $response = put(
        "/api/company/$model->id",
        [
            'name' => 'test3',
        ],
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );


    $response->assertStatus(200)->assertJson([
        'data' => [
            '0' => [
                'name' => 'test3',
                'email' => 'test@test.com',
                'phone_number' => '0834234',
            ]
        ]
    ]);
});

test('delete data null id company', function () use (&$token) {
    $response = delete(
        '/api/company/999',
        [],
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );


    $response->assertStatus(404)->assertJson(['message' => 'Data Can\'t be Found']);
});

test('delete data company', function () use (&$token) {
    createData($token);
    $model = DB::table('companies')->latest('id')->first();
    $response = delete(
        "/api/company/$model->id",
        [],
        [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]
    );


    $response->assertStatus(200)->assertJson(['message' => 'Data successfully erased']);
});
