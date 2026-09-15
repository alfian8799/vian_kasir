<?php

return [

    'title' => 'Signup',

    'heading' => 'Signup',

    'actions' => [

        'login' => [
            'before' => '',
            'label' => 'Already have an acount? Login',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Enter your email',
        ],

        'name' => [
            'label' => 'Enter your name',
        ],

        'password' => [
            'label' => 'Create a pasword',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirm your pasword',
        ],

        'actions' => [

            'register' => [
                'label' => 'Signup',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak permintaan',
            'body' => 'Silakan coba lagi dalam :seconds detik.',
        ],

    ],

];
