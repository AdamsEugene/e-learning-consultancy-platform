<?php
namespace App\Controllers\Reviews;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Reviews extends LoadController {

    /**
     * List reviews
     * 
     * @return void
     */
    public function list() {

        $data = [];

        // get the params that has been set to be used in the query
        foreach(['course_id', 'user_id'] as $item) {
            if(!empty($this->payload[$item])) {
                $data[$item] = $this->payload[$item];
            }
        }

        // get the reviews
        $reviews = $this->reviewsModel->getRecords(
            $this->payload['limit'] ?? $this->defaultLimit, 
            $this->payload['offset'] ?? $this->defaultOffset,
            $data,
            true
        );

        // return the reviews
        return Routing::success(formatCourseReviews($reviews));
    }

    /**
     * Create review
     * 
     * @return void
     */
    public function create() {
    
        // get the reviews
        $reviews = $this->reviewsModel->getRecords(1, 0, ['course_id' => $this->payload['course_id'], 'user_id' => $this->currentUser['user_id']]);

        // check if the user has already reviewed the course
        if(!empty($reviews)) {
            return Routing::error('You have already reviewed this course');
        }

        // set the user id
        $this->payload['user_id'] = $this->currentUser['user_id'];

        // create the review
        $reviewId = $this->reviewsModel->createRecord($this->payload);

        // return the success message
        return Routing::created([
            'data' => 'Review created successfully',
            'record' => $this->reviewsModel->getRecord($reviewId)
        ]);
    }

    /**
     * Delete review
     * 
     * @return void
     */
    public function delete() {

        // delete the review
        $payload = ['id' => $this->payload['review_id']];

        // check if the user is an admin
        if(!is_admin($this->currentUser)) {
            $payload['user_id'] = $this->currentUser['user_id'];
        }

        // confirm if the review exists
        $review = $this->reviewsModel->getRecords(1, 0, $payload);

        // check if the review exists
        if(empty($review)) {
            return Routing::error('Review not found');
        }

        // delete the review
        $this->reviewsModel->deleteRecord($payload);

        // return the success message
        return Routing::success('Review deleted successfully');
    }
     
}
?>