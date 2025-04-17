<?php

namespace App\Controllers\Wishlist;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Wishlist extends LoadController {    
    
    /**
     * View
     * 
     * @return void
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
        return Routing::success($wishList);
    }

    /**
     * View
     * 
     * @return void
     */
    public function view() {

        // get the payload
        $payload = ['wishlist_id' => $this->payload['id']];

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

        // return the wishlist record
        return Routing::success($wishList);
    }

    /**
     * Delete
     * 
     * @return void
     */
    public function delete() {

        // get the payload
        $payload = ['wishlist_id' => $this->payload['id']];

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
        $this->wishlistModel->deleteRecord($this->payload['id']);

        return Routing::success('Wishlist deleted successfully');
    }

}