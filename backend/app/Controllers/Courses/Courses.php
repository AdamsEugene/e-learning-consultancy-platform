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

        // confirm if the category set exists
        $category = $this->categoriesModel->getRecords(2, 0, null, ['course_ids' => [$this->payload['category_id'], $this->payload['subcategory_id'] ?? 0]]);
        if(empty($category)) {
            return Routing::error('Category not found');
        }
        
        // confirm if the subcategory set exists
        if(!empty($this->payload['subcategory_id']) && count($category) < 2) {
            return Routing::error('Subcategory not found');
        }

        // create course
        $courseId = $this->coursesModel->createRecord($this->payload);

        // get instructor id
        $instructor_id = $this->currentUser['id'];

        // get instructor id
        if(!is_admin($this->currentUser)) {
            $instructor_id = $this->payload['instructor_id'];
        }

        // get the tags
        if(!empty($this->payload['tags'])) {
            $tags = stringToArray($this->payload['tags']);
            foreach($tags as $tag) {
                if(!empty($tag) && preg_match("/^[0-9]+$/", $tag)) {
                    $tagValue = $this->tagsModel->getRecord($tag);
                    if(!empty($tagValue)) {
                        unset($tagValue['created_at']);
                        unset($tagValue['updated_at']);
                        $tags[] = $tagValue;
                    }
                }
            }
            $this->payload['tags'] = json_encode($tags ?? []);
        }

        // insert the course instructors
        $this->instructorsModel->createRecord([
            'course_id' => $courseId,
            'instructor_id' => $instructor_id
        ]);

        if(!empty($this->payload['instructors'])) {
            $instructors = stringToArray($this->payload['instructors']);
            foreach($instructors as $instructor) {
                if(!empty($instructor) && preg_match("/^[0-9]+$/", $instructor)) {
                    $this->instructorsModel->createRecord([
                        'course_id' => $courseId,
                        'instructor_id' => $instructor
                    ]);
                }
            }
        }

        // set course id
        $this->payload['course_id'] = $courseId;

        return Routing::created([
            'data' => 'Course created successfully',
            'record' => $this->view($courseId)
        ]);
        
    }

}