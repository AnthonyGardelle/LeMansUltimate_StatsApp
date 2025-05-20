<?php

return [
    'test_name' => 'An account for :first_name :name already exists. Please log in.',
    'custom' => [
        'email' => [
            'unique' => 'An account with this email address already exists. Please log in.',
        ],
        'password' => [
            'min' => 'The password must be at least :min characters long.',
        ],
    ],
];
