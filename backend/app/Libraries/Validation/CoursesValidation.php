<?php
namespace App\Libraries\Validation;

class CoursesValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'payload' => [
                "limit" => "permit_empty|integer",
                "offset" => "permit_empty|integer",
                "search" => "permit_empty|string|max_length[255]",
                "data" => "permit_empty|is_array",
            ]
        ],
        'view:course_id' => [
            'method' => 'GET',
            'payload' => [
                "course_id" => "required|string|max_length[255]",
            ]
        ]
    ];

}
?>