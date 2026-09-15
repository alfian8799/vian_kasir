<?php

return [

    'title' => 'Login',

    'heading' => 'Login',

    'actions' => [

        'register' => [
            'before' => '',
            'label' => "Don't have an acount? Signup",
        ],

        'request_password_reset' => [
            'label' => 'Forgot pasword?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Enter your email',
        ],

        'password' => [
            'label' => 'Enter your pasword',
        ],

        'remember' => [
            'label' => 'Remember me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Login',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Verifikasi identitas Anda',

        'subheading' => 'Untuk melanjutkan login, Anda perlu memverifikasi identitas Anda.',

        'form' => [

            'provider' => [
                'label' => 'Bagaimana Anda ingin memverifikasi?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Konfirmasi login',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Kredensial yang diberikan tidak dapat ditemukan.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak permintaan',
            'body' => 'Silakan coba lagi dalam :seconds detik.',
        ],

    ],

];
