<?php 

namespace App\Libraries\Validation;

class DiscussionsValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'limit' => 'permit_empty|integer',
                'offset' => 'permit_empty|integer',
                'course_id' => 'required|integer',
                'parent_id' => 'permit_empty|integer',
                'lesson_id' => 'permit_empty|integer',
                'search' => 'permit_empty|string'
            ]
        ],
        'view:discussion_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'discussion_id' => 'required|integer',
                'lesson_id' => 'permit_empty|integer',
                'course_id' => 'permit_empty|integer',
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'text' => 'required|string',
                'parent_id' => 'permit_empty|integer',
                'course_id' => 'required|integer',
                'lesson_id' => 'required|integer',
            ]
        ],
        'update:discussion_id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'payload' => [
                'text' => 'required|string',
                'discussion_id' => 'required|integer',
            ]
        ],
        'delete:discussion_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'discussion_id' => 'required|integer',
            ]
        ],
        'upVote:discussion_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'discussion_id' => 'required|integer',
            ]
        ],
        'downVote:discussion_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'discussion_id' => 'required|integer',
            ]
        ]
    ];

}