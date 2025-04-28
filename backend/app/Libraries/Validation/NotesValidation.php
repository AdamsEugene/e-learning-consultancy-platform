<?php 

namespace App\Libraries\Validation;

class NotesValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'limit' => 'permit_empty|integer',
                'offset' => 'permit_empty|integer',
                'course_id' => 'required|integer',
                'lesson_id' => 'permit_empty|integer',
                'search' => 'permit_empty|string'
            ]
        ],
        'view:note_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'note_id' => 'required|integer',
                'lesson_id' => 'required|integer',
                'course_id' => 'permit_empty|integer',
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'text' => 'required|string',
                'course_id' => 'required|integer',
                'lesson_id' => 'required|integer',
            ]
        ],
        'update:note_id' => [
            'method' => 'PUT',
            'authenticate' => true,
            'payload' => [
                'text' => 'required|string',
                'note_id' => 'required|integer',
            ]
        ],
        'delete:note_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'note_id' => 'required|integer',
            ]
        ]
    ];

}