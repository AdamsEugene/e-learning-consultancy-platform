<?php

namespace App\Controllers\Courses;

use App\Libraries\Routing;
use App\Controllers\LoadController;

class Enrollments extends LoadController {

    protected $coursesModel;

    /**
     * Set the properties
     * 
     * @return void
     */
    public function setProps($payload, $uniqueId, $currentUser, $coursesModel) {
        $this->payload = $payload;
        $this->uniqueId = $uniqueId;
        $this->currentUser = $currentUser;
        $this->coursesModel = $coursesModel;
    }

    /**
     * Enroll in a course
     * 
     * @return void
     */
    public function enroll() {

        // get the course
        $course = $this->coursesModel->getRecord($this->uniqueId);

        if(empty($course)) {
            return Routing::notFound();
        }
        
        // check if the user is not already enrolled in the course
        $enrollment = $this->enrollmentsModel->getRecords([
            'user_id' => $this->currentUser['user_id'], 
            'course_id' => $this->uniqueId, 
            'status' => ['Enrolled', 'Pending']
        ]);

        if(!empty($enrollment)) {
            return Routing::error('You are already enrolled in this course');
        }

        // use this as the default until the application of a coupon
        $finalPrice = $course['price'];

        // set the status
        $payload = [
            'status' => $course['course_type'] == 'free' ? 'Enrolled' : 'Pending',
            'course_id' => $this->uniqueId,
            'user_id' => $this->currentUser['user_id'],
            'amount_payable' => $finalPrice,
            'amount_offered' => $course['price']
        ];

        if($payload['status'] == 'Enrolled')  {
            $this->coursesModel->updateRecord(['enrollmentCount' => $course['enrollmentCount'] + 1], $this->uniqueId);
        }

        return $payload;

        // return $course;
        
        
    }

}
