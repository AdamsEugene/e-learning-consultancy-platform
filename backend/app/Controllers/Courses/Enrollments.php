<?php

namespace App\Controllers\Courses;

use App\Libraries\Routing;
use App\Controllers\LoadController;

class Enrollments extends LoadController {

    protected $coursesModel;
    protected $coursesController;

    /**
     * Set the properties
     * 
     * @return array
     */
    public function setProps($payload = [], $uniqueId = null, $currentUser = [], $coursesModel = null, $coursesController = null) {
        $this->payload = $payload;
        $this->uniqueId = $uniqueId;
        $this->currentUser = $currentUser;
        $this->coursesModel = $coursesModel;
        $this->coursesController = $coursesController;
    }

    /**
     * Get the list of all enrolled courses
     * 
     * @return array
     */
    public function list($courseData = []) {
        
        // init variables
        $payload = ['user_id' => $this->currentUser['user_id']];

        // check if the course id is set
        foreach(['course_id', 'category_id', 'status'] as $key) {
            if(!empty($this->payload[$key])) {
                $payload[$key] = $this->payload[$key];
            }
        }

        if(!empty($this->payload['enroll_id'])) {
            $payload['id'] = $this->payload['enroll_id'];
        }

        // is single check
        $single = !empty($this->payload['course_id']) || !empty($this->payload['enroll_id']) ? true : false;

        // check if the user id is set
        $enrollments = $this->enrollmentsModel->getRecords($payload);

        // fix the no enrollments record
        if(empty($enrollments)) {
            return Routing::success([]);
        }

        if(!empty($this->payload['enroll_id'])) {
            // set the minified to true
            $this->coursesController->minified = false;
            $this->coursesController->uniqueId = $enrollments[0]['course_id'];

            // get the course data
            $courseData = $this->coursesController->view()['data'];

            $enrollments[0]['courseInfo'] = $courseData;
        }

        return Routing::success(formatEnrolledCourses($enrollments, $single));
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

    /**
     * Start learning
     * 
     * @return array
     */
    public function startlearning() {

        // get the enrollment
        $enrollment = $this->enrollmentsModel->getRecords([
            'user_id' => $this->currentUser['user_id'], 
            'id' => $this->payload['enroll_id'], 
            'status' => ['Enrolled', 'Pending']
        ]);

        // check if the enrollment is empty
        if(empty($enrollment)) {
            return Routing::error('You are not enrolled in this course');
        }

        // reset all params for the course
        $this->enrollmentsModel->updateRecord($this->payload['enroll_id'], [
            'lessonsCompleted' => 0,
            'currentLesson' => 1,
            'nextLesson' => 1,
            'status' => 'Started'
        ]);

        return Routing::success('You have started learning the course');
    }

    /**
     * Continue learning
     * 
     * @return array
     */
    public function lessonlog() {

        // get the enrollment
        $enrollment = $this->enrollmentsModel->getRecords([
            'user_id' => $this->currentUser['user_id'], 
            'id' => $this->payload['enroll_id'], 
            'status' => ['Enrolled', 'Pending']
        ]);

        // check if the enrollment is empty
        if(empty($enrollment)) {
            return Routing::error('You are not enrolled in this course');
        }
        
        

    }
}