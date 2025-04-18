<?php 
namespace App\Libraries\Validation;

class CategoriesValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'payload' => [
                "status" => "permit_empty|string|max_length[255]",
            ]
        ],
        'view:id' => [
            'method' => 'GET',
            'payload' => [
                "id" => "required|integer",
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "name" => "required|string|max_length[255]",
                "description" => "permit_empty|string|max_length[255]",
                "image" => "permit_empty|string|max_length[255]",
                "status" => "permit_empty|string|max_length[255]",
                "parent_id" => "permit_empty|integer",
            ]
        ],
        'update:id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "id" => "required|integer",
                "name" => "permit_empty|string|max_length[255]",
                "description" => "permit_empty|string|max_length[255]",
                "image" => "permit_empty|string|max_length[255]",
                "status" => "permit_empty|string|max_length[255]",
                "parent_id" => "permit_empty|integer",
            ]
        ],
        'delete:id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "id" => "required|integer",
            ]
        ]
    ];

}