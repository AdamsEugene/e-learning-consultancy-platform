<?php

namespace App\Models;

use App\Models\DbTables;
use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class DiscussionsModel extends Model {

    public $isAdmin = false;
    protected $table;
    protected $primaryKey = 'id';
    protected $coursesTable;
    protected $userTable;
    protected $allowedFields = ['user_id', 'course_id', 'lesson_id', 'discussion_hash', 'votes', 'parent_id', 'content', 'created_at', 'updated_at'];

    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$discussionsTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }
    
    /**
     * Get records from the notes table
     * @param array $data
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getRecords($data = [], $offset = 0, $limit = 100) {

        try {

            // get the table name
            $query = $this->db->table("{$this->table} a")->select("a.*,
                (SELECT JSON_OBJECT(
                    'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email, 'phone', u.phone, 'image', u.image
                ) FROM {$this->userTable} u WHERE u.id = a.user_id LIMIT 1
            ) as created_by");

            // loop through the data
            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $query->whereIn("a.{$key}", $value);
                } else {
                    $query->where("a.{$key}", $value);
                }
            }

            // get the results
            return $query->orderBy('parent_id', 'ASC')
                        ->orderBy('created_at', 'DESC')
                        ->limit($limit, $offset)->get()->getResultArray();

        } catch(DatabaseException $e) {
            return [];
        }
        
    }

    /**
     * Get a record from the notes table
     * @param int $id
     * @param array $data
     * @return array
     */
    public function getRecord($id, $data = []) {

        try {

            $query = $this->db->table("{$this->table} a")->select("a.*,
                (SELECT JSON_OBJECT(
                    'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email, 'phone', u.phone, 'image', u.image
                ) FROM {$this->userTable} u WHERE u.id = a.user_id LIMIT 1
            ) as created_by")->where('id', $id);

            // loop through the data
            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $query->whereIn("a.{$key}", $value);
                } else {
                    $query->where("a.{$key}", $value);
                }
            }

            return $query->get()->getRowArray();

        } catch(DatabaseException $e) {
            return [];
        }

    }

    /**
     * Create a record in the notes table
     * @param array $data
     * @return int
     */
    public function createRecord($data) {

        try {

            $this->insert($data);
            return $this->getInsertID();

        } catch(DatabaseException $e) {}

    }

    /**
     * Update a record in the notes table
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateRecord($id, $data) {

        try {

            $this->update($id, $data);
            return $this->affectedRows();

        } catch(DatabaseException $e) {}

    }

    /**
     * Delete a record from the notes table
     * @param int $id
     * @return int
     */
    public function deleteRecord($id, $data = []) {

        try {

            $this->where('id', $id);

            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $this->whereIn($key, $value);
                } else {
                    $this->where($key, $value);
                }
            }

            $this->delete();
            return $this->affectedRows();

        } catch(DatabaseException $e) {}

    }   

}