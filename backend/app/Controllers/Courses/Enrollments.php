<?php

namespace App\Controllers\Courses;

use App\Libraries\Routing;
use App\Controllers\LoadController;

class Enrollments extends LoadController {

    protected $coursesModel;

    /**
     * Set the properties
     * 
     * @return array
     */
    public function setProps($payload = [], $uniqueId = null, $currentUser = [], $coursesModel = null) {
        $this->payload = $payload;
        $this->uniqueId = $uniqueId;
        $this->currentUser = $currentUser;
        $this->coursesModel = $coursesModel;
    }

    /**
     * Get the list of all enrolled courses
     * 
     * @return array
     */
    public function list() {
        
        // init variables
        $payload = ['user_id' => $this->currentUser['user_id']];

        // check if the course id is set
        foreach(['course_id', 'category_id', 'status'] as $key) {
            if(!empty($this->payload[$key])) {
                $payload[$key] = $this->payload[$key];
            }
        }

        // check if the user id is set
        $enrollments = $this->enrollmentsModel->getRecords($payload);

        return Routing::success(formatEnrolledCourses($enrollments));
    }

    /**
     * Enroll in a course
     * 
     * @return array
     */
    public function enroll($courseData) {

        // get the course
        $course = $courseData['course'];
        $sections = $courseData['sections'];

        // init variables
        $sectionsCount = 0;
        $lessonsCount = 0;

        // count the sections and lessons
        if(!empty($sections) && is_array($sections)) {
            foreach($sections as $section) {
                $sectionsCount++;
                $lessonsCount += count($section['lessons']);
            }
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
            'amountPayable' => $finalPrice,
            'lessonsCount' => $lessonsCount,
            'sectionsCount' => $sectionsCount,
            'amountOffered' => $course['price']
        ];

        // insert the enrollment
        $this->enrollmentsModel->createRecord($payload);

        if($payload['status'] == 'Enrolled')  {
            $this->coursesModel->updateRecord($this->uniqueId, ['enrollmentCount' => $course['enrollmentCount'] + 1]);
        }

        return Routing::success('You have been enrolled in the course');
    }

}
