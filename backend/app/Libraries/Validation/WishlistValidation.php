<?php
namespace App\Libraries\Validation;

class WishlistValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'search' => 'string|max_length[255]',
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'course_id' => 'required|integer|greater_than[0]',
            ]
        ],
        'view:wishlist_id' => [
            'method' => 'GET',
            'authenticate' => true,
            'payload' => [
                'wishlist_id' => 'required|integer|greater_than[0]',
            ]
        ],
        'delete:wishlist_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'wishlist_id' => 'required|integer|greater_than[0]',
            ]
        ]
    ];

}
?>