<?php 

namespace App\Controllers\Instructors;

use App\Controllers\LoadController;
use App\Libraries\Routing;
use App\Controllers\Courses\Courses;
class Instructors extends LoadController {

    /**
     * List users
     * 
     * @return array
     */
    public function list() {

        // set the default data
        $data['user_type'] = 'Instructor';

        // get the users
        $users = $this->usersModel->findUsers(
            $this->payload['limit'] ?? $this->defaultLimit, 
            $this->payload['offset'] ?? 0,
            $this->payload['search'] ?? null,
            stringToArray($this->payload['status'] ?? 'active'),
            [],
            $data
        );

        // return the success message
        return Routing::success(formatUserResponse($users));
    }

    /**
     * View instructor
     * 
     * @return array
     */
    public function view() {

        $this->triggerModel('courses');

        // get the instructor
        $instructor = $this->usersModel->findById($this->uniqueId, ['active'], ['user_type' => 'Instructor']);

        if(empty($instructor)) {
            return Routing::notFound();
        }

        $courseObject = new Courses();
        $courseObject->payload['created_by'] = $instructor['id'];
        $courses = $courseObject->list();

        $instructor = formatUserResponse([$instructor], true);
        $instructor['courses'] = $courses['data'];
        // return the success message
        return Routing::success($instructor);
    }
}