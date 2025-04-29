<?php

namespace App\Controllers\Discussions;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Discussions extends LoadController {

    /**
     * List discussions
     * 
     * @return array
     */
    public function list() {

        // Get the payload
        if(!is_admin($this->currentUser)) {
            $payload = ['user_id' => $this->currentUser['user_id']];
        }

        // Get the payload
        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $payload = ['user_id' => $this->payload['user_id']];
        }

        foreach(['course_id', 'lesson_id', 'parent_id'] as $key) {
            if(!empty($this->payload[$key])) {
                $payload[$key] = $this->payload[$key];
            }
        }

        // Get the discussions
        $discussions = $this->discussionsModel->getRecords($payload, $this->payload['offset'], $this->payload['limit']);

        // Return the discussions
        return Routing::success(formatDiscussions($discussions));

    }

    /**
     * View a discussion
     * 
     * @return array
     */
    public function view() {

        // Get the payload
        if(!is_admin($this->currentUser)) {
            $payload = ['user_id' => $this->currentUser['user_id']];
        }

        // Get the payload
        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $payload = ['user_id' => $this->payload['user_id']];
        }

        foreach(['course_id', 'lesson_id'] as $key) {
            if(!empty($this->payload[$key])) {
                $payload[$key] = $this->payload[$key];
            }
        }

        // Get the discussion
        $discussion[] = $this->discussionsModel->getRecord($this->payload['discussion_id'], $payload);

        if(empty($discussion)) {
            return Routing::notFound();
        }

        if($discussion[0]['parent_id'] == 0) {
            $getChildren = $this->discussionsModel->getRecords([
                'parent_id' => $discussion[0]['id'],
                'course_id' => $discussion[0]['course_id'],
                'lesson_id' => $discussion[0]['lesson_id'],
            ]);
            foreach($getChildren as $child) {
                $discussion[] = $child;
            }
        }

        // Return the discussion
        return Routing::success(formatDiscussions($discussion));

    }

    /**
     * Create a discussion
     * 
     * @return array
     */
    public function create() {

        $this->triggerModel(['courses']);

        $payload = [
            'content' => trim($this->payload['text']),
            'course_id' => $this->payload['course_id'],
            'lesson_id' => $this->payload['lesson_id'],
            'parent_id' => $this->payload['parent_id'] ?? 0,
            'user_id' => $this->currentUser['user_id'],
        ];

        // get the course sections and lessons
        $lesson = $this->coursesModel->getSections(['course_id' => $payload['course_id']]);

        if(empty($lesson)) {
            return Routing::notFound();
        }

        // Get the lesson ids
        $getLessonsList = array_column($lesson, 'lessons');
        $lessonIds = [];
        foreach($getLessonsList as $lesson) {
            $less = json_decode($lesson, true);
            foreach($less as $l) {
                $lessonIds[] = $l['id'];
            }
        }

        // Check if the lesson id is valid
        if(!in_array($payload['lesson_id'], $lessonIds)) {
            return Routing::notFound();
        }

        // Create the discussion hash
        $payload['discussion_hash'] = md5($payload['content']);

        // check if the discussion already exists
        $discussion = $this->discussionsModel->getRecords([
            'discussion_hash' => $payload['discussion_hash'], 
            'lesson_id' => $payload['lesson_id'],
            'user_id' => $payload['user_id'],
            'parent_id' => $payload['parent_id']
        ]);
        if(!empty($discussion)) {
            return Routing::updated('Discussion already exists', $discussion[0]);
        }

        // Create the discussion
        $discussionId = $this->discussionsModel->createRecord($payload);

        // Get the discussion
        $this->payload['discussion_id'] = $discussionId;
        $this->payload['user_id'] = $this->currentUser['user_id'];

        // log the count
        $this->analyticsObject->logCount('Discussions');

        return Routing::created([
            'data' => 'Discussion created successfully',
            'record' => $this->view()['data'],
        ]);

    }

    /**
     * Update a discussion
     * 
     * @return array
     */
    public function update() {

        $payload = ['content' => trim($this->payload['text'])];
        $payload['discussion_hash'] = md5($payload['content']);

        // Get the where
        if(!is_admin($this->currentUser)) {
            $where['user_id'] = $this->currentUser['user_id'];
        }

        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $where['user_id'] = $this->payload['user_id'];
        }
        
        // Update the discussion
        $this->discussionsModel->updateRecord($this->payload['discussion_id'], $payload, $where);

        // Return the discussion
        return Routing::updated('Discussion updated successfully', $this->view()['data'][0]);

    }

    /**
     * Delete a discussion
     * 
     * @return array
     */
    public function delete() {

        // Get the where
        if(!is_admin($this->currentUser)) {
            $where['user_id'] = $this->currentUser['user_id'];
        }

        // Get the where
        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $where['user_id'] = $this->payload['user_id'];
        }

        // confirm if the discussion exists
        $discussion = $this->discussionsModel->getRecord($this->payload['discussion_id'], $where);
        if(empty($discussion)) {
            return Routing::notFound();
        }

        // Delete the discussion
        $this->discussionsModel->deleteRecord($this->payload['discussion_id'], $where);

        // log the count
        $this->analyticsObject->logCount('Discussions', 'decrement');

        // Return the discussion
        return Routing::success('Discussion deleted successfully');

    }

    /**
     * Upvote a discussion
     * 
     * @return array
     */
    public function upVote() {

        // confirm if the discussion exists
        $discussion = $this->discussionsModel->getRecord($this->payload['discussion_id']);
        if(empty($discussion)) {
            return Routing::notFound();
        }

        // upvote the discussion
        $this->discussionsModel->upVote($this->payload['discussion_id']);

        // log the count
        return Routing::success('Discussion upvoted successfully');

    }

    /**
     * Downvote a discussion
     * 
     * @return array
     */
    public function downVote() {

        // confirm if the discussion exists
        $discussion = $this->discussionsModel->getRecord($this->payload['discussion_id']);
        if(empty($discussion)) {
            return Routing::notFound();
        }

        // downvote the discussion
        $this->discussionsModel->downVote($this->payload['discussion_id']);

        return Routing::success('Discussion downvoted successfully');

    }
}