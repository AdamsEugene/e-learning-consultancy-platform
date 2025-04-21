<?php

namespace App\Controllers\Courses;

use App\Controllers\LoadController;
use App\Controllers\Courses\Enrollments;

use App\Libraries\Routing;

class Courses extends LoadController {

    // do update course
    public $doUpdateCourse = false;
    public $recordInfo = [];
    public $minified = false;

    // get all the sections
    public $allSections = ['id', 'title', 'lessons', 'totalDuration'];
    public $allLessons = ['id', 'title', 'duration', 'videoUrl', 'type'];
    public $acceptedLessonTypes = ["video", "quiz", "assignment", "resource"];

    public $defaultStatus = 'Unpublished';
    public $statusList = ['Unpublished', 'Under Review', 'Published', 'Archived'];

    /**
     * List courses
     * 
     * @return array
     */
    public function list() {

        // set the default status of the course if no status is set
        if(empty($this->payload['data'])) {
            $this->payload['data'] = ['status' => $this->statusList];
        }

        // loop through the payload and get the courses by the created by
        foreach(['created_by', 'status'] as $key) {
            if(!empty($this->payload[$key])) {
                $this->payload['data'][$key] = $this->payload[$key];
            }
        }

        // get courses
        $courses = $this->coursesModel->getRecords(
            $this->payload['limit'] ?? $this->defaultLimit,
            $this->payload['offset'] ?? $this->defaultOffset,
            $this->payload['search'] ?? null,
            $this->payload['data'] ?? []
        );

        // return response
        return Routing::success(formatCourseResponse($courses, $this->payload['minified'] ?? false));
    }

    /**
     * View course
     * 
     * @return array
     */
    public function view() {

        // trigger models
        $this->triggerModel(['instructors', 'reviews']);

        $courseId = !empty($this->uniqueId) ? $this->uniqueId : $this->payload['course_id'];

        // get course
        $course = $this->coursesModel->getRecord($courseId);

        if(empty($course)) {
            return Routing::notFound();
        }

        // get course sections
        $created_by = empty($course['id']) ? [] : formatUserResponse([$this->usersModel->findById($course['created_by'])], true, true);
        $instructors = empty($course['id']) || $this->minified ? [] : $this->instructorsModel->getRecords(100, 0, ['course_id' => $course['id']]);
        $reviews = empty($course['id']) || $this->minified ? [] : $this->reviewsModel->getRecordByCourseId(100, 0, ['record_id' => $course['id'], 'entityType' => 'Course']);
        $sections = empty($course['id']) ? [] : $this->coursesModel->getSections(['course_id' => $course['id']]);

        // return response
        return Routing::success([
            'course' => empty($course) ? [] : formatCourseResponse([$course], false, true,),
            'created_by' => empty($created_by) ? [] : $created_by,
            'instructors' => empty($instructors) ? [] : $instructors,
            'reviews' => empty($reviews) ? [] : $reviews,
            'sections' => empty($sections) ? [] : formatCourseSections($sections)
        ]);
    }

    /**
     * Create course
     * 
     * @return array
     */
    public function create() {

        // trigger models
        $this->triggerModel(['categories', 'instructors', 'tags']);

        // confirm if the category set exists
        if(isset($this->payload['category_id']) || isset($this->payload['subcategory_id'])) {
            $category = $this->categoriesModel->getRecords(['active'], ['category_ids' => [$this->payload['category_id'], $this->payload['subcategory_id'] ?? 0]]);
            if(empty($category)) {
                return Routing::error('Category not found');
            }
        }

        // confirm if the user is an admin or an instructor
        if(!in_array($this->currentUser['user_type'], ['Admin', 'Instructor'])) {
            // return Routing::error('You are not authorized to create a course. Only admins and instructors can create courses.');
        }

        // compare the price and the original price
        if(!empty($this->payload['price'])) {
            if(!empty($this->payload['originalPrice']) && $this->payload['price'] > $this->payload['originalPrice']) {
                return Routing::error('Price cannot be greater than the original price');
            }
        }

        if(!empty($this->payload['title'])) {
            // create a title slug
            $this->payload['title_slug'] = url_title($this->payload['title'], '-', true);

            // create the slug
            $create_slug = url_title($this->payload['title'], '-', true);

            // check if the slug already exists
            $check_slug = $this->coursesModel->getRecordBySlug($create_slug);
            if(!empty($check_slug)) {
                $this->payload['title_slug'] = $create_slug . '-' . random_string('alnum', 5);
            }
        }

        // convert the payload to json if it is an array
        foreach(['what_you_will_learn', 'requirements', 'features', 'description'] as $key) {
            if(!empty($this->payload[$key]) && is_array($this->payload[$key])) {
                $this->payload[$key] = json_encode($this->payload[$key]);
            }
        }

        // check if the price is 0
        if(isset($this->payload['price']) &&$this->payload['price'] == 0) {
            $this->payload['course_type'] = 'free';
        }
        
        // confirm if the subcategory set exists
        if(!empty($this->payload['subcategory_id']) && count($category) < 2) {
            return Routing::error('Subcategory not found');
        }

        // set the created by
        if(!$this->doUpdateCourse) {
            $this->payload['created_by'] = $this->currentUser['user_id'];
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
                        $tags_list[] = [
                            'id' => $tagValue['id'],
                            'name' => $tagValue['name'],
                            'name_slug' => $tagValue['name_slug'],
                            'color' => $tagValue['color']
                        ];
                    }
                }
            }
            $this->payload['tags'] = json_encode($tags_list ?? []);
        }

        // reprocess the sections
        $reprocessSections = true;

        // validate the sections
        if(!empty($this->payload['sections'])) {
            // decode the sections
            $sections = is_array($this->payload['sections']) ? $this->payload['sections'] : json_decode($this->payload['sections'], true);

            // validate the sections
            $validateSections = validateCourseSections($sections, $this->allSections, $this->allLessons, $this->acceptedLessonTypes);
            if(is_string($validateSections)) {
                return Routing::error($validateSections);
            }

            if(!empty($this->recordInfo)) {
                $sections = $this->coursesModel->getSections(['course_id' => $this->recordInfo['id']], 100, 0, 'ASC');

                $existing = [];
                if(!empty($sections)) {
                    foreach($sections as $section) {
                        $existing[] = [
                            'title' => $section['title'],
                            'lessons' => $section['lessons'],
                            'totalDuration' => $section['totalDuration']
                        ];
                    }
                }

                $incoming = [];
                foreach($validateSections as $section) {
                    $incoming[] = [
                        'title' => $section['title'],
                        'lessons' => json_encode($section['lessons']),
                        'totalDuration' => $section['totalDuration']
                    ];
                }

                if(md5(json_encode($incoming)) == md5(json_encode($existing))) {
                    $reprocessSections = false;
                }
            }

            // set the sections
            $sectionsList = $validateSections;
        }

        // set the default status of the course to unpublished
        $this->payload['status'] = $this->payload['status'] ?? ($this->recordInfo['status'] ?? $this->defaultStatus);
        
        if(!is_admin($this->currentUser)) {
            $this->payload['status'] = $this->recordInfo['status'] ?? $this->defaultStatus;
        }

        // create course
        if($this->doUpdateCourse) {
            $this->coursesModel->updateRecord($this->uniqueId, $this->payload);
            $courseId = $this->uniqueId;
        } else {
            $courseId = $this->coursesModel->createRecord($this->payload);
        }

        // get instructor id
        $instructor_id = $this->currentUser['user_id'];

        // get instructor id
        if(is_admin($this->currentUser) && !empty($this->payload['instructor_id'])) {
            $instructor_id = $this->payload['instructor_id'];
        }

        // insert the sections
        if(!empty($sectionsList) && is_array($sectionsList) && $reprocessSections) {

            // if updating, delete the existing sections
            if($this->doUpdateCourse) {
                $this->coursesModel->deleteSection(['course_id' => $courseId]);
            }
            
            // loop through the sections
            foreach($sectionsList as $section) {
                $this->coursesModel->createSection([
                    'course_id' => $courseId,
                    'title' => $section['title'],
                    'lessons' => json_encode($section['lessons']),
                    'totalDuration' => $section['totalDuration'],
                    'totalLessons' => count($section['lessons'])
                ]);
            }
        }
        // insert the course instructors
        if(!$this->doUpdateCourse || $this->doUpdateCourse && !empty($this->payload['instructor_id'])) {

            // delete the existing instructors
            if($this->doUpdateCourse && !empty($this->payload['instructor_id'])) {
                $this->instructorsModel->deleteRecord(['course_id' => $courseId, 'instructor_id' => $instructor_id]);
            }

            // create the new instructors
            $this->instructorsModel->createRecord([
                'course_id' => $courseId,
                'instructor_id' => $instructor_id
            ]);
        }

        // get the instructors
        if(!empty($this->payload['instructors'])) {
            $instructors = stringToArray($this->payload['instructors']);
            foreach($instructors as $instructor) {
                if(!empty($instructor) && preg_match("/^[0-9]+$/", $instructor)) {
                    if((int)$instructor_id == (int)$instructor) continue;
                    $this->instructorsModel->createRecord([
                        'course_id' => $courseId,
                        'instructor_id' => $instructor
                    ]);
                }
            }
        }

        // set course id
        $this->payload['course_id'] = $courseId;

        // log the count
        $this->analyticsObject->logCount('Courses');

        return Routing::created([
            'data' => $this->doUpdateCourse ? 'Course updated successfully' : 'Course created successfully',
            'record' => $this->view($courseId)['data']
        ]);
        
    }

    /**
     * Update course
     * 
     * @return array
     */
    public function update() {

        // do update course
        $this->doUpdateCourse = true;

        // get the record infomation
        $this->recordInfo = $this->coursesModel->getRecord($this->uniqueId);

        if(empty($this->recordInfo)) {
            return Routing::notFound();
        }

        return $this->create();
    }

    /**
     * Enroll in a course
     * 
     * @return array
     */
    public function enroll() {

        // create a new instance of the enrollments controller
        $enrolObject = new Enrollments();
        $enrolObject->setProps($this->payload, $this->uniqueId, $this->currentUser, $this->coursesModel);

        $this->minified = true;
        $courseData = $this->view();

        if($courseData['statusCode'] !== 200) {
            return $courseData;
        }

        // get the course data
        $courseData = $courseData['data'];

        // return the response and procesing the request
        return $enrolObject->enroll($courseData);

    }

    /**
     * Get the list of all enrolled courses
     * 
     * @return array
     */
    public function enrolled() {
        
        // create a new instance of the enrollments controller
        $enrolObject = new Enrollments();
        $enrolObject->setProps($this->payload, $this->uniqueId, $this->currentUser, $this->coursesModel, $this);

        // check if the course id is set
        if(!empty($this->payload['course_id'])) {

            // set the minified to true
            $this->minified = true;

            // get the course data
            $courseData = $this->view();

            // check if the course data is valid
            if($courseData['statusCode'] !== 200) {
                return $courseData;
            }

            // get the course data
            $courseData = $courseData['data'];
        }

        // return the response and procesing the request
        return $enrolObject->list($courseData ?? []);
    }

    /**
     * Start learning
     * 
     * @return array
     */
    public function startlearning() {

        // create a new instance of the enrollments controller
        $enrolObject = new Enrollments();
        $enrolObject->setProps($this->payload, $this->uniqueId, $this->currentUser, $this->coursesModel, $this);

        // return the response and procesing the request
        return $enrolObject->startlearning();
    }

    /**
     * Continue learning
     * 
     * @return array
     */
    public function lessonlog() {

        // create a new instance of the enrollments controller
        $enrolObject = new Enrollments();
        $enrolObject->setProps($this->payload, $this->uniqueId, $this->currentUser, $this->coursesModel, $this);

        // return the response and procesing the request
        return $enrolObject->lessonlog();
    }

    /**
     * Update course status
     * 
     * @return array
     */
    public function statuses() {

        // confirm if the course exists
        $course = $this->coursesModel->getRecord($this->mainRawId);
        if(empty($course)) {
            return Routing::notFound();
        }

        // confirm if the status is valid
        if($course['status'] == $this->payload['status']) {
            return Routing::error('Course is already in this status.');
        }

        // if the incoming status is published, increment the courses count
        if(($this->payload['status'] == 'Published')) {
            $this->usersModel->db->query("UPDATE users SET coursesCount = (coursesCount + 1) WHERE id = ? LIMIT 1", [$course['created_by']]);
        }

        // if the course is published and the status is not unpublished, decrement the courses count
        if($course['status'] == 'Published' && $this->payload['status'] !== 'Published') {
            $this->usersModel->db->query("UPDATE users SET coursesCount = (coursesCount - 1) WHERE id = ? LIMIT 1", [$course['created_by']]);
        }

        // update the status of the course
        $this->coursesModel->updateRecord($this->mainRawId, ['status' => $this->payload['status']]);

        // return response
        return Routing::success('Course status updated successfully');
    }

    /**
     * Delete course
     * 
     * @return array
     */
    public function delete() {

        // confirm if the course exists
        $course = $this->coursesModel->getRecord($this->uniqueId);
        if(empty($course)) {
            return Routing::notFound();
        }

        // delete the course
        $this->coursesModel->deleteRecord($this->uniqueId);

        // log the count
        $this->analyticsObject->logCount('Courses', 'decrement');

        // return response
        return Routing::success('Course deleted successfully');
    }
}