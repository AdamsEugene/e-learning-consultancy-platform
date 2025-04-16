<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class CategoriesModel extends Model {

    protected $table = 'categories';
    protected $allowedFields = ['name', 'description', 'parent_id', 'image', 'created_by', 'created_at', 'updated_at', 'courses_count', 'status', 'name_slug'];
    
    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$categoriesTable;
    }
    
    /**
     * Get all categories
     * 
     * @param array $status
     * @return array
     */
    public function getRecords($status = ['active']) {
        try {
            return $this->whereIn('status', $status)->orderBy('preferred_order', 'ASC')->findAll();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get a category
     * 
     * @param int $id
     * @param string $status
     * @return array
     */
    public function getRecord($id, $status = 'active') {
        try {
            return $this->where(['id' => $id, 'status' => $status])->first();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get a category by slug
     * 
     * @param string $slug
     * @param string $status
     * @return array
     */
    public function getRecordBySlug($slug, $status = 'active') {
        try {
            return $this->where(['name_slug' => $slug, 'status' => $status])->first();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create a category
     * 
     * @param array $data
     * @return int
     */
    public function createRecord($data) {
        try {
            $this->insert($data);
            return $this->getInsertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Delete a category
     * 
     * @param int $id
     * @return bool
     */
    public function deleteRecord($id) {
        try {
            return $this->update($id, ['status' => 'deleted', 'updated_at' => date('Y-m-d H:i:s')]);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update a category
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateRecord($id, $data) {
        try {
            return $this->update($id, $data);
        } catch(DatabaseException $e) {
            return false;
        }
    }
}