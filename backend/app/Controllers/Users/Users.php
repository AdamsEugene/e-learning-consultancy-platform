<?php 

namespace App\Controllers\Users;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Users extends LoadController {


    /**
     * List users
     * 
     * @return array
     */
    public function list() {

        // get the user ids
        $userIds = $this->payload['user_ids'] ?? [];

        // if the current user is a student, get the user ids
        if(is_student($this->currentUser)) {
            $userIds = [$this->currentUser['id']];
        }

        // get the users
        $users = $this->usersModel->findUsers(
            $this->payload['limit'] ?? $this->defaultLimit, 
            $this->payload['offset'] ?? 0,
            $this->payload['search'] ?? null,
            stringToArray($this->payload['status'] ?? 'active'),
            stringToArray($userIds)
        );

        // return the success message
        return Routing::success(formatUserResponse($users));
    }

    /**
     * View a user
     * 
     * @return array
     */
    public function view() {

        // check if the user id is set
        $users = $this->usersModel->findById($this->payload['user_id']);

        if(empty($users)) {
            return Routing::notFound();
        }

        return Routing::success(formatUserResponse([$users], true));
    }

    /**
     * Create a new user
     * 
     * @return array
     */
    public function create() {

        // Check if email already exists
        $confirmEmail = $this->usersModel->findByEmail($this->payload['email']);
        
        // Check if email already exists
        if ($confirmEmail) {
            return Routing::error('Email already exists');
        }

        // hash the password
        $hashPassword = hash_password($this->payload['password']);

        // set the password
        $this->submittedPayload['password'] = $hashPassword;
        $this->submittedPayload['user_type'] = ucwords($this->submittedPayload['user_type']);

        // set the username
        $this->submittedPayload['username'] = generateUsername($this->payload['email']);

        // remove the password confirm
        unset($this->submittedPayload['password_confirm']);

        // create the user
        $userId = $this->usersModel->createRecord($this->submittedPayload);

        // set the user id
        $this->payload['user_id'] = $userId;

        return Routing::created([
            'data' => 'The user has been created successfully', 
            'record' => $this->view()['data']
        ]);

    }

    /**
     * Update a user
     * 
     * @return array
     */
    public function update() {

        // check if the user id is set
        $users = $this->usersModel->findById($this->payload['user_id']);

        if(empty($users)) {
            return Routing::notFound();
        }

        // remove the user id
        unset($this->submittedPayload['user_id']);

        // if the current user is not an admin, set the user id to the current user id
        if(!is_admin($this->currentUser)) {
            $this->payload['user_id'] = $this->currentUser['id'];
        }

        // check if the email is set
        if(!empty($this->submittedPayload['email'])) {
            $confirmEmail = $this->usersModel->findByEmail($this->submittedPayload['email']);
            if ($confirmEmail) {
                return Routing::error('Email already exists');
            }
        }

        // remove the user type, two factor setup and two factor secret
        foreach(['user_type', 'two_factor_setup', 'twofactor_secret', 'date_registered', 'status'] as $item) {
            if(isset($this->submittedPayload[$item])) {
                unset($this->submittedPayload[$item]);
            }
        }

        // check if the password is set
        if(!empty($this->submittedPayload['password'])) {
            $hashPassword = hash_password($this->submittedPayload['password']);
            $this->submittedPayload['password'] = $hashPassword;
        }

        foreach(['social_links', 'preferences'] as $item) {
            if(isset($this->submittedPayload[$item])) {
                $this->submittedPayload[$item] = json_encode($this->submittedPayload[$item]);
            }
        }

        // update the user information
        $this->usersModel->updateRecord($this->payload['user_id'], $this->submittedPayload);

        // return the success message
        return Routing::updated([
            'data' => 'The user has been updated successfully', 
            'record' => $this->view()['data']
        ]);
    }

    /**
     * Deactivate a user
     * 
     * @return array
     */
    public function deactivate() {

        // check if the user id is set
        $users = $this->usersModel->findById($this->payload['user_id']);

        if(empty($users)) {
            return Routing::notFound();
        }

        // run a series of db queries to deactivate the user

        // also set the account for deletion in 30 days
    }

    /**
     * Delete a user
     * 
     * @return array
     */
    public function delete() {

        // check if the user id is set
        $users = $this->usersModel->findById($this->payload['user_id']);

        if(empty($users)) {
            return Routing::notFound();
        }

        // delete the user
        $this->usersModel->updateRecord($this->payload['user_id'], ['status' => 'deleted']);

        // return the success message
        return Routing::success('The user has been deleted successfully');
    }

}

?>