<?php

namespace App\Controllers\Courses;

use App\Controllers\LoadController;

use App\Libraries\Routing;

class Courses extends LoadController {

    /**
     * List courses
     * 
     * @return void
     */
    public function list() {
        // trigger models
        $this->triggerModel(['courses']);

        // get courses
        $courses = $this->coursesModel->getRecords(
            $this->payload['limit'] ?? 12,
            $this->payload['offset'] ?? 0,
            $this->payload['search'] ?? null,
            $this->payload['data'] ?? []
        );

        // return response
        return Routing::success($courses);
    }

    /**
     * View course
     * 
     * @return void
     */
    public function view() {

        // trigger models
        $this->triggerModel(['courses', 'instructors', 'reviews']);

        // get course
        $course = $this->coursesModel->getRecord($this->payload['course_id']);

        if(empty($course)) {
            return Routing::notFound();
        }

        // get course sections
        $created_by = empty($course['id']) ? [] : formatUserResponse($this->usersModel->findById($course['created_by']));
        $instructors = empty($course['id']) ? [] : $this->instructorsModel->getRecords(100, 0, ['course_id' => $course['id']]);
        $reviews = empty($course['id']) ? [] : $this->reviewsModel->getRecordByCourseId(100, 0, ['course_id' => $course['id']]);

        // return response
        return Routing::success([
            'course' => empty($course) ? [] : $course,
            'created_by' => empty($created_by) ? [] : $created_by,
            'instructors' => empty($instructors) ? [] : $instructors,
            'reviews' => empty($reviews) ? [] : $reviews
        ]);
    }

    /**
     * Create course
     * 
     * @return void
     */
    public function create() {
        // trigger models
        $this->triggerModel(['courses']);

        // create course
        $courseId = $this->coursesModel->createRecord($this->payload);

        // get instructor id
        $instructor_id = $this->currentUser['id'];

        // get instructor id
        if(!is_admin($this->currentUser)) {
            $instructor_id = $this->payload['instructor_id'];
        }

        // insert the course instructors
        $this->instructorsModel->createRecord([
            'course_id' => $courseId,
            'instructor_id' => $instructor_id
        ]);

        // set course id
        $this->payload['course_id'] = $courseId;

        return Routing::created([
            'data' => 'Course created successfully',
            'record' => $this->view($courseId)
        ]);
    }

}