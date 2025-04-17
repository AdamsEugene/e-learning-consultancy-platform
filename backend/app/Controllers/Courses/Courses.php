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
        return Routing::success(formatCourseResponse($courses));
    }

    /**
     * View course
     * 
     * @return void
     */
    public function view() {

        // trigger models
        $this->triggerModel(['courses', 'instructors', 'reviews']);

        $courseId = !empty($this->mainRawId) ? $this->mainRawId : $this->payload['course_id'];

        // get course
        $course = $this->coursesModel->getRecord($courseId);

        if(empty($course)) {
            return Routing::notFound();
        }

        // get course sections
        $created_by = empty($course['id']) ? [] : formatUserResponse([$this->usersModel->findById($course['created_by'])], true, true);
        $instructors = empty($course['id']) ? [] : $this->instructorsModel->getRecords(100, 0, ['course_id' => $course['id']]);
        $reviews = empty($course['id']) ? [] : $this->reviewsModel->getRecordByCourseId(100, 0, ['course_id' => $course['id']]);

        // return response
        return Routing::success([
            'course' => empty($course) ? [] : formatCourseResponse([$course]),
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
        $this->triggerModel(['courses', 'categories', 'instructors', 'tags']);

        // confirm if the category set exists
        $category = $this->categoriesModel->getRecords(['active'], ['category_ids' => [$this->payload['category_id'], $this->payload['subcategory_id'] ?? 0]]);
        if(empty($category)) {
            return Routing::error('Category not found');
        }

        // compare the price and the original price
        if(!empty($this->payload['originalPrice']) && $this->payload['price'] > $this->payload['originalPrice']) {
            return Routing::error('Price cannot be greater than the original price');
        }

        // create a title slug
        $this->payload['title_slug'] = url_title($this->payload['title'], '-', true);

        // create the slug
        $create_slug = url_title($this->payload['title'], '-', true);

        // check if the slug already exists
        $check_slug = $this->coursesModel->getRecordBySlug($create_slug);
        if(!empty($check_slug)) {
            $this->payload['title_slug'] = $create_slug . '-' . random_string('alnum', 5);
        }

        // convert the payload to json if it is an array
        foreach(['what_you_will_learn', 'requirements', 'features', 'description'] as $key) {
            if(!empty($this->payload[$key]) && is_array($this->payload[$key])) {
                $this->payload[$key] = json_encode($this->payload[$key]);
            }
        }

        // check if the price is 0
        if($this->payload['price'] == 0) {
            $this->payload['course_type'] = 'free';
        }
        
        // confirm if the subcategory set exists
        if(!empty($this->payload['subcategory_id']) && count($category) < 2) {
            return Routing::error('Subcategory not found');
        }

        // set the created by
        $this->payload['created_by'] = $this->currentUser['user_id'];

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

        // create course
        $courseId = $this->coursesModel->createRecord($this->payload);

        // get instructor id
        $instructor_id = $this->currentUser['user_id'];

        // get instructor id
        if(is_admin($this->currentUser) && !empty($this->payload['instructor_id'])) {
            $instructor_id = $this->payload['instructor_id'];
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
            'record' => $this->view($courseId)['data']
        ]);
        
    }

}