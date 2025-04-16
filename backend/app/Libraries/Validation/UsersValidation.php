<?php

namespace App\Libraries\Validation;

class UsersValidation {

    public $routes = [
        'deactivate:user_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                "user_id" => "required|integer"
            ]
        ],
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                "limit" => "permit_empty|integer",
                "offset" => "permit_empty|integer",
                "search" => "permit_empty|string",
                "status" => "permit_empty|string",
            ]
        ],
        'delete:user_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "user_id" => "required|integer"
            ]
        ],
        'view:user_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                "user_id" => "required|integer"
            ]
        ],
        'create' => [
            'method' => 'POST',
            'payload' => [
                "firstname" => "required|string|max_length[255]",
                "lastname" => "required|string|max_length[255]",
                "email" => "required|string|max_length[255]",
                "phone" => "permit_empty|string|max_length[255]",
                "user_type" => "permit_empty|string|max_length[255]|in_list[Client|Student|Consultant|Instructor|Admin]",
                "password" => "required|valid_password|max_length[255]",
                "password_confirm" => "required|valid_password|max_length[255]|matches[password]",
            ]
        ],
        'update:user_id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'payload' => [
                "user_id" => "required|integer",
                "description" => "permit_empty|string|max_length[2000]",
                "nationality" => "permit_empty|string|max_length[255]",
                "gender" => "permit_empty|string|max_length[255]|in_list[Male,Female]",
                "date_of_birth" => "permit_empty|string|max_length[255]|valid_date",
                "phone" => "permit_empty|string|max_length[255]",
                "billing_address" => "permit_empty|string|max_length[255]",
                "firstname" => "permit_empty|string|max_length[255]",
                "lastname" => "permit_empty|string|max_length[255]",
                "timezone" => "permit_empty|string|max_length[255]",
                "website" => "permit_empty|string|max_length[255]",
                "company" => "permit_empty|string|max_length[255]",
                "job_title" => "permit_empty|string|max_length[255]",
                "email" => "permit_empty|string|max_length[255]",
                "language" => "permit_empty|string|max_length[255]",
                "phone" => "permit_empty|string|max_length[255]",
                "preferences" => "permit_empty|is_array|max_length[5]",
                "social_links" => "permit_empty|is_array|max_length[5]",
                "skills" => "permit_empty|string|max_length[500]",
                "user_type" => "permit_empty|string|max_length[255|in_list[Client|Student|Consultant|Instructor|Admin]"
            ]
        ]
    ];

}

?>