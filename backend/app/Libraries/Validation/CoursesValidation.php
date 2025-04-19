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
                "status" => "permit_empty|string|in_list[Published,Draft,Archived,Under Review,Unpublished]",
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "title" => "required|string|max_length[255]",
                "subtitle" => "permit_empty|string|max_length[255]",
                "rating" => "permit_empty|integer",
                "tags" => "permit_empty",
                "level" => "required|string|in_list[Beginner,Intermediate,Advanced,All Levels]",
                "category_id" => "required|integer",
                "subcategory_id" => "permit_empty|integer",
                "course_type" => "permit_empty|in_list[free,paid]",
                "originalPrice" => "permit_empty|integer",
                "price" => "required|integer",
                "sections" => "permit_empty",
                "status" => "permit_empty|string|in_list[Published,Draft,Archived,Under Review,Unpublished]",
                "instructors" => "permit_empty",
                "features" => "permit_empty",
                "description" => "permit_empty",
                "course_duration" => "permit_empty|integer",
                "what_you_will_learn" => "permit_empty",
                "requirements" => "permit_empty",
            ]
        ],
        'statuses:course_id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "course_id" => "required|string|max_length[255]",
                "status" => "required|string|in_list[Published,Draft,Archived,Under Review,Unpublished]"
            ]
        ],
        'view:course_id' => [
            'method' => 'GET',
            'payload' => [
                "course_id" => "required|string|max_length[255]",
            ]
        ],
        'update:course_id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "title" => "permit_empty|string|max_length[255]",
                "subtitle" => "permit_empty|string|max_length[255]",
                "rating" => "permit_empty|integer",
                "tags" => "permit_empty",
                "level" => "permit_empty|string|in_list[Beginner,Intermediate,Advanced,All Levels]",
                "category_id" => "permit_empty|integer",
                "subcategory_id" => "permit_empty|integer",
                "course_type" => "permit_empty|in_list[free,paid]",
                "originalPrice" => "permit_empty|integer",
                "price" => "permit_empty|integer",
                "sections" => "permit_empty",
                "instructors" => "permit_empty",
                "features" => "permit_empty",
                "description" => "permit_empty",
                "course_duration" => "permit_empty|integer",
                "what_you_will_learn" => "permit_empty",
                "requirements" => "permit_empty",
                "status" => "permit_empty|string|in_list[Published,Draft,Archived,Under Review,Unpublished]",
            ]
        ],
        'enroll:course_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                "course_id" => "required|string|max_length[12]",
                "user_id" => "permit_empty|string|max_length[12]",
            ]
        ],
        'delete:course_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'isAdmin' => true,
            'payload' => [
                "course_id" => "required|string|max_length[255]",
            ]
        ]
    ];

}
?>