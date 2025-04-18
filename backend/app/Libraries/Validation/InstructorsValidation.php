<?php

namespace App\Libraries\Validation;

class InstructorsValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'payload' => [
                "limit" => "permit_empty|integer",
                "offset" => "permit_empty|integer",
                "search" => "permit_empty|string",
                "status" => "permit_empty|string",
                "user_type" => "permit_empty|string",
            ]
        ],
        'view:id' => [
            'method' => 'GET',
            'payload' => [
                "id" => "required|integer",
            ]
        ],
    ];
}