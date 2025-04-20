<?php

namespace App\Controllers\Wishlist;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Wishlist extends LoadController {    
    
    /**
     * View
     * 
     * @return array
     */
    public function list() {

        // get the payload
        $payload = [];

        // if the user is not admin, then add the user id to the payload
        if(!is_admin($this->currentUser)) {
            $payload['user_id'] = $this->currentUser['user_id'];
        }

        // if the user is admin, then add the user id to the payload
        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $payload['user_id'] = $this->payload['user_id'];
        }

        // if the course id is provided, then add it to the payload
        if(!empty($this->payload['course_id'])) {
            $payload['course_id'] = $this->payload['course_id'];
        }

        // get the wishlist record
        $wishList = $this->wishlistModel->getRecords(
            $this->payload['limit'] ?? $this->defaultLimit, 
            $this->payload['offset'] ?? $this->defaultOffset, 
            $payload
        );

        // return the wishlist record
        return Routing::success(formatWishlistResponse($wishList));
    }

    /**
     * View
     * 
     * @return array
     */
    public function view() {

        // get the payload
        $payload = ['id' => $this->payload['wishlist_id']];

        // if the user is not admin, then add the user id to the payload
        if(!is_admin($this->currentUser)) {
            $payload['user_id'] = $this->currentUser['user_id'];
        }

        // get the wishlist record
        $wishList = $this->wishlistModel->getRecords(1, 0, $payload);

        // if the wishlist record is not found
        if(empty($wishList)) {
            return Routing::notFound();
        }

        // create a new instance of the courses controller
        $courseObject = new \App\Controllers\Courses\Courses();
        
        // set the course id
        $this->payload['course_id'] = $wishList[0]['course_id'];

        // set the payload
        $courseObject->payload = $this->payload;

        // get the course info
        $courseInfo = $courseObject->view()['data'];

        // return the wishlist record
        return Routing::success([
            'wishlist' => formatWishlistResponse($wishList, true),
            'course_info' => $courseInfo
        ]);
    }

    /**
     * Create
     * 
     * @return array
     */
    public function create() {

        $this->triggerModel('courses');

        // get the payload
        $payload = ['course_id' => $this->payload['course_id']];

        // confirm if the course exists
        $course = $this->coursesModel->getRecord($this->payload['course_id']);

        // if the course does not exist, then return an error
        if(empty($course)) {
            return Routing::error('Course not found');
        }

        // if the user is not admin, then add the user id to the payload
        $payload['user_id'] = $this->currentUser['user_id'];

        // if the user is admin, then add the user id to the payload
        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $payload['user_id'] = $this->payload['user_id'];
        }

        // check if the records for this user already exist
        $wishlist = $this->wishlistModel->getRecords(1, 0, $payload);

        // if the records for this user already exist, then return an error
        if(!empty($wishlist)) {
            return Routing::error('This course is already in your wishlist');
        }

        // create the wishlist record
        $wishlistId = $this->wishlistModel->createRecord($payload);

        $this->payload['wishlist_id'] = $wishlistId;

        return Routing::created([
            'data' => 'Wishlist created successfully',
            'record' => $this->view()['data']
        ]);
    }

    /**
     * Delete
     * 
     * @return array
     */
    public function delete() {

        // get the payload
        $payload = ['wishlist_id' => $this->payload['wishlist_id']];

        // if the user is not admin, then add the user id to the payload
        if(!is_admin($this->currentUser)) {
            $payload['user_id'] = $this->currentUser['id'];
        }

        // get the wishlist record
        $wishList = $this->wishlistModel->getRecords(1, 0, $payload);

        // if the wishlist record is not found
        if(empty($wishList)) {
            return Routing::notFound();
        }

        // delete the wishlist record
        $this->wishlistModel->deleteRecord($this->payload['wishlist_id']);

        return Routing::success('Wishlist deleted successfully');
    }

}