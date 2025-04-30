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
                'class_date' => 'required|string|valid_date',
                'start_time' => 'required|string|max_length[5]',
                'meeting_type' => 'required|string|in_list[Online,In Person]',
                'end_time' => 'required|string|max_length[5]',
                'maximum_participants' => 'permit_empty|integer',
                'class_duration' => 'permit_empty|string',
                'recurring_interval' => 'permit_empty|string|in_list[daily,weekly,bi-weekly,monthly,yearly]',
                'recurring_end_date' => 'permit_empty|string|valid_date',
                'is_recurring' => 'permit_empty|in_list[yes,no]',
                'class_link' => 'permit_empty|string|valid_url_strict',
                'class_password' => 'permit_empty|string',
                'materials' => 'permit_empty',
                'students_list' => 'permit_empty|string',
                'notify_participants' => 'permit_empty|in_list[yes,no]'
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
                'class_date' => 'permit_empty|string|valid_date',
                'start_time' => 'permit_empty|string|max_length[5]',
                'end_time' => 'permit_empty|string|max_length[5]',
                'maximum_participants' => 'permit_empty|integer',
                'meeting_type' => 'permit_empty|string|in_list[Online,In Person]',
                'class_duration' => 'permit_empty|string',
                'recurring_interval' => 'permit_empty|string|in_list[daily,weekly,bi-weekly,monthly,yearly]',
                'recurring_end_date' => 'permit_empty|string|valid_date',
                'is_recurring' => 'permit_empty|in_list[yes,no]',
                'class_link' => 'permit_empty|string|valid_url_strict',
                'class_password' => 'permit_empty|string',
                'materials' => 'permit_empty',
                'students_list' => 'permit_empty|string',
                'notify_participants' => 'permit_empty|in_list[yes,no]'
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