<?php

return [
    'test_name' => 'Un compte pour :first_name :name existe déjà. Veuillez vous connecter.',
    'custom' => [
        'email' => [
            'unique' => 'Un compte avec cette adresse e-mail existe déjà. Veuillez vous connecter.',
        ],
        'password' => [
            'min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ],
    ],
];
