<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class WishlistModel extends Model {

    protected $table;
    protected $coursesTable;
    protected $allowedFields = ['user_id', 'course_id'];
    
    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$wishlistTable;
        $this->coursesTable = DbTables::$coursesTable;
    }

    /**
     * Get records
     * 
     * @param int $limit
     * @param int $offset
     * @param array $data
     * @return array
     */
    public function getRecords($limit = 10, $offset = 0, $data = []) {

        $columns = "";
        foreach(['title', 'title_slug', 'image', 'thumbnail', 'rating', 'tags', 'features'] as $column) {
            $columns .= "{$this->coursesTable}.{$column} as course_{$column},";
        }
        $columns = rtrim($columns, ',');

        // get query
        $query = $this->select("{$this->table}.*, {$columns}")
            ->join($this->coursesTable, "{$this->coursesTable}.id = {$this->table}.course_id", 'left');

        if(isset($data['user_id'])) {
            $query->where("{$this->table}.user_id", $data['user_id']);
        }

        if(!empty($data['wishlist_id'])) {
            $query->where("{$this->table}.id", $data['wishlist_id']);
        }

        if(isset($data['course_id'])) {
            $query->where("{$this->table}.course_id", $data['course_id']);
        }

        $query->orderBy("{$this->table}.id", 'DESC');

        return $query->findAll($limit, $offset);
    }

    /**
     * Get record
     * 
     * @param array $data
     * 
     * @return array
     */
    public function getRecord($data) {
        try {
            $columns = "";
            foreach(['title', 'title_slug', 'image', 'thumbnail', 'rating', 'tags', 'features'] as $column) {
                $columns .= "{$this->coursesTable}.{$column} as course_{$column},";
            }
            $columns = rtrim($columns, ',');

            // get query
            $query = $this->select("{$this->table}.*, {$columns}")
                ->join($this->coursesTable, "{$this->coursesTable}.id = {$this->table}.course_id", 'left')
                ->where($data);

            return $query->first();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create record
     * 
     * @param array $data
     * @return int
     */
    public function createRecord($data) {
        try {
            $this->insert($data);
            return $this->getInsertID();
        } catch(DatabaseException $e) {
            return 0;
        }
    }
    
    /**
     * Delete record
     * 
     * @param int $id
     * @return bool
     */
    public function deleteRecord($id) {
        try {
            return $this->where('id', $id)->delete();
        } catch(DatabaseException $e) {
            return false;
        }
    }
}