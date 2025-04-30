<?php

namespace App\Libraries\Validation;

class ActivitiesValidation {

    public $routes = [
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'activity_type' => 'required|string',
                'section' => 'required|string',
                'content' => 'required|string'
            ]
        ],
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => []
        ],
        'view:activity_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'activity_id' => 'required|integer'
            ]
        ]
    ];

}