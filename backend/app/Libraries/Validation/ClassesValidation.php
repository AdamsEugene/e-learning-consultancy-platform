<?php

namespace App\Libraries\Validation;

class ClassesValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'user_id' => 'permit_empty|integer',
                'course_id' => 'permit_empty|integer',
                'class_type' => 'permit_empty|string',
                'class_date' => 'permit_empty|string',
                'status' => 'permit_empty|string',
                'limit' => 'permit_empty|integer',
                'offset' => 'permit_empty|integer'
            ]
        ],
        'view:class_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'class_id' => 'required|integer'
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'user_id' => 'permit_empty|integer',
                'course_id' => 'required|integer',
                'title' => 'required|string',
                'description' => 'required|string',
                'class_type' => 'required|string',
                'class_date' => 'required|string',
                'class_time' => 'permit_empty|string',
                'class_duration' => 'permit_empty|string',
                'class_link' => 'permit_empty|string',
                'class_password' => 'permit_empty|string',
                'materials' => 'permit_empty|string',
                'students_list' => 'permit_empty|string'
            ]
        ],
        'update:class_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'class_id' => 'required|integer',
                'user_id' => 'permit_empty|integer',
                'course_id' => 'required|integer',
                'title' => 'required|string',
                'description' => 'required|string',
                'class_type' => 'required|string',
                'class_date' => 'required|string',
                'class_time' => 'permit_empty|string',
                'class_duration' => 'permit_empty|string',
                'class_link' => 'permit_empty|string',
                'class_password' => 'permit_empty|string',
                'materials' => 'permit_empty|string',
                'students_list' => 'permit_empty|string'
            ]
        ],
        'delete:class_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'class_id' => 'required|integer'
            ]
        ],
        'recordAttendance:class_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'class_id' => 'required|integer',
                'user_id' => 'required|integer'
            ]
        ],
        'removeAttendance:class_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'class_id' => 'required|integer',
                'user_id' => 'required|integer'
            ]
        ],
        'listAttendance:class_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'class_id' => 'required|integer'
            ]
        ]
    ];
}