<?php

namespace App\Libraries\Validation;

class ResourcesValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'user_id' => 'permit_empty|integer',
                'record_id' => 'permit_empty|integer',
                'record_type' => 'permit_empty|string',
                'limit' => 'permit_empty|integer',
                'offset' => 'permit_empty|integer'
            ]
        ],
        'view:resource_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'resource_id' => 'required|integer'
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'user_id' => 'permit_empty|integer',
                'record_id' => 'required|integer',
                'record_type' => 'required|string',
                'file_name' => 'required|string',
                'file_path' => 'required|string',
                'file_type' => 'required|string',
                'file_size' => 'required|integer'
            ]
        ],
        'delete:resource_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'resource_id' => 'required|integer'
            ]
        ]
    ];
}