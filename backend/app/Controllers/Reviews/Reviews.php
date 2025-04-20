<?php
namespace App\Controllers\Reviews;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Reviews extends LoadController {

    /**
     * List reviews
     * 
     * @return array
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
     * @return array
     */
    public function create() {
    
        $this->triggerModel('courses');

        // set the entity type
        $this->payload['entityType'] = $this->payload['entityType'] ?? 'Course';

        // get the reviews
        $reviews = $this->reviewsModel->getRecords(1, 0, [
            'record_id' => $this->payload['record_id'], 
            'user_id' => $this->currentUser['user_id'],
            'entityType' => $this->payload['entityType']
        ]);

        // check if the user has already reviewed the course
        if(!empty($reviews)) {
            return Routing::error('You have already reviewed this ' . $this->payload['entityType']);
        }

        // set the user id
        $this->payload['user_id'] = $this->currentUser['user_id'];

        // create the review
        $reviewId = $this->reviewsModel->createRecord($this->payload);

        // table name to use
        $tableName = $this->payload['entityType'] == 'Course' ? $this->coursesModel->table : $this->coursesModel->userTable;

        // increment the review count for the course
        $this->coursesModel->db->query("UPDATE {$tableName} SET reviewCount = (reviewCount + 1) WHERE id = {$this->payload['record_id']}");

        // log the count
        $this->analyticsObject->logCount('Reviews');

        // return the success message
        return Routing::created([
            'data' => 'Review created successfully',
            'record' => $this->reviewsModel->getRecord($reviewId)
        ]);
    }

    /**
     * Delete review
     * 
     * @return array
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

        // log the count
        $this->analyticsObject->logCount('Reviews', 'decrement');

        // return the success message
        return Routing::success('Review deleted successfully');
    }
     
}
?>