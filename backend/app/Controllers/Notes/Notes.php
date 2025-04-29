<?php

namespace App\Controllers\Notes;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Notes extends LoadController {

    /**
     * List the notes
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

        foreach(['course_id', 'lesson_id'] as $key) {
            if(!empty($this->payload[$key])) {
                $payload[$key] = $this->payload[$key];
            }
        }

        // Get the notes
        $notes = $this->notesModel->getRecords($payload, $this->payload['offset'], $this->payload['limit']);

        // Return the notes
        return Routing::success($notes);

    }

    /**
     * View a note
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

        // Get the note
        $note = $this->notesModel->getRecord($this->payload['note_id'], $payload);

        // Return the note
        return Routing::success($note);

    }

    /**
     * Create a note
     * 
     * @return array
     */
    public function create() {

        $this->triggerModel(['courses']);

        $payload = [
            'content' => trim($this->payload['text']),
            'course_id' => $this->payload['course_id'],
            'lesson_id' => $this->payload['lesson_id'],
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

        // Create the note hash
        $payload['note_hash'] = md5($payload['content']);


        // check if the note already exists
        $note = $this->notesModel->getRecords([
            'note_hash' => $payload['note_hash'], 
            'lesson_id' => $payload['lesson_id'],
            'user_id' => $payload['user_id']
        ]);
        if(!empty($note)) {
            return Routing::updated('Note already exists', $note[0]);
        }

        // Create the note
        $noteId = $this->notesModel->createRecord($payload);

        // Get the note
        $this->payload['note_id'] = $noteId;
        $this->payload['user_id'] = $this->currentUser['user_id'];

        // log the count
        $this->analyticsObject->logCount('Notes');

        return Routing::created([
            'data' => 'Note created successfully',
            'record' => $this->view()['data'],
        ]);

    }

    /**
     * Update a note
     * 
     * @return array
     */
    public function update() {

        $payload = ['content' => trim($this->payload['text'])];
        $payload['note_hash'] = md5($payload['content']);

        // Get the where
        if(!is_admin($this->currentUser)) {
            $where['user_id'] = $this->currentUser['user_id'];
        }

        if(is_admin($this->currentUser) && !empty($this->payload['user_id'])) {
            $where['user_id'] = $this->payload['user_id'];
        }
        
        // Update the note
        $this->notesModel->updateRecord($this->payload['note_id'], $payload, $where);

        // Return the note
        return Routing::updated('Note updated successfully', $this->view()['data']);
    }

    /**
     * Delete a note
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

        // confirm if the note exists
        $note = $this->notesModel->getRecord($this->payload['note_id'], $where);
        if(empty($note)) {
            return Routing::notFound();
        }

        // Delete the note
        $this->notesModel->deleteRecord($this->payload['note_id'], $where);

        // log the count
        $this->analyticsObject->logCount('Notes', 'decrement');

        // Return the note
        return Routing::success('Note deleted successfully');
    }
    
}