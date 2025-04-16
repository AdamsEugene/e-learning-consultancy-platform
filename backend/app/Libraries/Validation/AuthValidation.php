<?php

namespace App\Libraries\Validation;

class AuthValidation {

    public $routes = [
        'confirm' => [
            'method' => 'POST,GET',
            'authenticate' => true,
            'payload' => []
        ],
        'login' => [
            'method' => 'POST',
            'payload' => [
                'email' => 'required|valid_email|max_length[100]',
                'password' => 'required|min_length[8]|max_length[32]',
                'rememberme' => 'in_list[0,1]'
            ]
        ],
        'logout' => [
            'method' => 'GET,POST',
            'payload' => [
                'token' => 'required|max_length[255]'
            ]
        ],
        'forgotten' => [
            'method' => 'POST',
            'payload' => [
                'email' => 'required|valid_email|max_length[100]'
            ]
        ],
        'reset' => [
            'method' => 'POST',
            'payload' => [
                'email' => 'required|valid_email|max_length[100]',
                'password' => 'required|valid_password|min_length[8]|max_length[32]',
                'password_confirm' => 'required|matches[password]',
                'code' => 'required|max_length[6]|numeric'
            ]
        ],
        'verify' => [
            'method' => 'POST',
            'payload' => [
                'code' => 'required|max_length[6]|numeric',
                'email' => 'required|valid_email|max_length[100]'
            ]
        ],
        'setup2fa' => [
            'method' => 'POST',
            'authenticate' => true
        ],
        'disable2fa' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'user_id' => 'permit_empty|numeric'
            ]
        ],
        'verify2fa' => [
            'method' => 'POST',
            'payload' => [
                'user_id' => 'permit_empty|numeric',
                'is_login' => 'permit_empty|in_list[0,1]',
                'code' => 'required|max_length[6]|numeric',
                'secret' => 'required|max_length[32]|alpha_numeric_space'
            ]
        ]
    ];

}