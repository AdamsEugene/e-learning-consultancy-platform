<?php

namespace App\Controllers\Tags;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Tags extends LoadController {
    
    /**
     * List all categories
     * 
     * @return array
     */
    public function list() {

        // trigger models
        $this->triggerModel(['tags']);
        
        // get the categories
        $tags = $this->tagsModel->getRecords();

        // return the categories
        return Routing::success($tags);

    }
    
    /**
     * View a category
     * 
     * @param int $id
     * @return array
     */
    public function view($id) {

        // trigger models
        $this->triggerModel(['tags']);

        // get the category
        $tag = $this->tagsModel->getRecord($id);

        if(empty($tag)) {
            return Routing::notFound('Tag not found', true);
        }

        // return the tag
        return Routing::success($tag);
    }

    /**
     * Create a category
     * 
     * @return array
     */
    public function create() {

        // trigger models
        $this->triggerModel(['tags']);

        // create the slug
        $create_slug = url_title($this->payload['name'], '-', true);

        // check if the slug already exists
        $check_slug = $this->tagsModel->getRecordBySlug($create_slug);
        if(!empty($check_slug)) {
            return Routing::error('Tag already exists');
        }

        // create the category
        $this->payload['name_slug'] = $create_slug;
        $this->payload['color'] = strtolower($this->payload['color']);

        // create the category
        $tagId = $this->tagsModel->createRecord($this->payload);

        // get the category
        return Routing::created([
            'data' => 'Tag created successfully',
            'record' => $this->tagsModel->getRecord($tagId)
        ]);

    }

    /**
     * Update a category
     * 
     * @param int $id
     * @return array
     */
    public function update() {

        // trigger models
        $this->triggerModel(['tags']);

        // get the category
        $tag = $this->tagsModel->getRecord($this->payload['id']);
        
        if(empty($tag)) {
            return Routing::notFound('Tag not found', true);
        }

        // update the last date
        $this->submittedPayload['updated_at'] = date('Y-m-d H:i:s');

        // update the tag
        $this->tagsModel->updateRecord($this->payload['id'], $this->submittedPayload);

        // return the tag
        return Routing::updated('Tag updated successfully', $this->tagsModel->getRecord($this->payload['id']));
    }

    /**
     * Delete a category
     * 
     * @param int $id
     * @return array
     */
    public function delete() {

        // trigger models
        $this->triggerModel(['tags']);

        // get the tag
        $tag = $this->tagsModel->getRecord($this->payload['id']);
        
        if(empty($tag)) {
            return Routing::notFound('Tag not found', true);
        }

        // delete the tag
        $this->tagsModel->deleteRecord($this->payload['id']);

        // return the label
        return Routing::deleted();
    }
}
?>