<?php

namespace App\Controllers\Categories;

use App\Controllers\LoadController;
use App\Libraries\Routing;

class Categories extends LoadController {
    
    /**
     * List all categories
     * 
     * @return array
     */
    public function list() {
        
        // trigger models
        $this->triggerModel(['categories']);

        // get the categories
        $categories = $this->categoriesModel->getRecords();

        // return the categories
        return Routing::success($categories);

    }
    
    /**
     * View a category
     * 
     * @param int $id
     * @return array
     */
    public function view($id) {

        // trigger models
        $this->triggerModel(['categories']);

        // get the category
        $category = $this->categoriesModel->getRecord($id);

        if(empty($category)) {
            return Routing::notFound('Category not found', true);
        }

        // return the category
        return Routing::success($category);
    }

    /**
     * Create a category
     * 
     * @return array
     */
    public function create() {

        // trigger models
        $this->triggerModel(['categories']);

        // create the slug
        $create_slug = url_title($this->payload['name'], '-', true);

        // check if the slug already exists
        $check_slug = $this->categoriesModel->getRecordBySlug($create_slug);
        if(!empty($check_slug)) {
            return Routing::error('Category already exists');
        }

        // set the created by
        if(!empty($this->currentUser['user_id'])) {
            $this->payload['created_by'] = $this->currentUser['user_id'];
        }

        // check if the parent category exists
        if(!empty($this->payload['parent_id'])) {
            $parentCategory = $this->categoriesModel->getRecord($this->payload['parent_id']);
            if(empty($parentCategory)) {
                return Routing::error('Parent category not found');
            }
        }

        // create the category
        $this->payload['name_slug'] = $create_slug;

        // create the category
        $categoryId = $this->categoriesModel->createRecord($this->payload);

        // log the count
        $this->analyticsObject->logCount('Categories');

        // get the category
        return Routing::created([
            'data' => 'Category created successfully',
            'record' => $this->categoriesModel->getRecord($categoryId)
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
        $this->triggerModel(['categories']);
        
        // get the category
        $category = $this->categoriesModel->getCategory($this->payload['id']);
        
        if(empty($category)) {
            return Routing::notFound('Category not found', true);
        }

        // check if the parent category exists
        if(!empty($this->payload['parent_id'])) {
            $parentCategory = $this->categoriesModel->getRecord($this->payload['parent_id']);
            if(empty($parentCategory)) {
                return Routing::error('Parent category not found');
            }
        }

        // update the last date
        $this->submittedPayload['updated_at'] = date('Y-m-d H:i:s');

        // update the category
        $this->categoriesModel->updateCategory($this->payload['id'], $this->submittedPayload);

        // return the category
        return Routing::updated('Category updated successfully', $this->categoriesModel->getCategory($this->payload['id']));
    }

    /**
     * Delete a category
     * 
     * @param int $id
     * @return array
     */
    public function delete() {

        // trigger models
        $this->triggerModel(['categories']);

        // get the category
        $category = $this->categoriesModel->getRecord($this->payload['id']);
        
        if(empty($category)) {
            return Routing::notFound('Category not found', true);
        }

        // delete the category
        $this->categoriesModel->deleteRecord($this->payload['id']);

        // log the count
        $this->analyticsObject->logCount('Categories', 'decrement');

        // return the category
        return Routing::deleted();
    }
}
?>