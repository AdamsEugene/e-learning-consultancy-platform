<?php
namespace App\Libraries\Validation;

class ReviewsValidation {

    public $routes = [
        'list' => [
            'method' => 'GET',
            'payload' => [
                'limit' => 'permit_empty|integer',
                'offset' => 'permit_empty|integer',
                'search' => 'permit_empty|string',
                'data' => 'permit_empty',
            ]
        ],
        'create' => [
            'method' => 'POST',
            'authenticate' => true,
            'payload' => [
                'course_id' => 'required|integer',
                'rating' => 'permit_empty|integer',
                'helpfulCount' => 'required|integer|less_than_equal_to[5]',
                'dislikesCount' => 'permit_empty|integer|less_than_equal_to[5]',
                'content' => 'required|string',
            ]
        ],
        'delete:review_id' => [
            'method' => 'DELETE',
            'authenticate' => true,
            'payload' => [
                'review_id' => 'required|integer',
            ]
        ]
    ];
}
