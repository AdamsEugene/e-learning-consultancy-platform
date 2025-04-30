<?php

namespace App\Libraries\Validation;

class NotificationsValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'user_id' => 'permit_empty|integer',
                'section' => 'permit_empty|string',
                'limit' => 'permit_empty|integer',
                'offset' => 'permit_empty|integer',
                'read' => 'permit_empty|in_list[yes,no]'
            ]
        ],
        'view:notification_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'notification_id' => 'required|integer'
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'user_id' => 'permit_empty|integer',
                'title' => 'required|string',
                'message' => 'required|string',
                'section' => 'required|string',
                'link' => 'permit_empty|string'
            ]
        ],
        'markAsRead:notification_id' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'notification_id' => 'required|integer'
            ]
        ],
        'markAllAsRead' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => []
        ],
        'delete:notification_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'notification_id' => 'required|integer'
            ]
        ]
    ];
}