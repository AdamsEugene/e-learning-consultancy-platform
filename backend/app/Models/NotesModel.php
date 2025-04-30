<?php

namespace App\Models;

use App\Models\DbTables;
use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class NotesModel extends Model {

    public $isAdmin = false;
    
    protected $table;
    protected $primaryKey = 'id';
    protected $coursesTable;
    protected $userTable;
    protected $allowedFields = ['user_id', 'course_id', 'lesson_id', 'note_hash', 'content', 'created_at', 'updated_at'];

    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$notesTable;
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

            $query = $this->select('*');

            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }

            return $query->orderBy('created_at', 'DESC')->limit($limit, $offset)->get()->getResultArray();

        } catch(DatabaseException $e) {}

    }

    /**
     * Get a record from the notes table
     * @param int $id
     * @param array $data
     * @return array
     */
    public function getRecord($id, $data = []) {

        try {

            // Get the note
            $query = $this->select('*')->where('id', $id);

            if(!empty($data)) {
                foreach($data as $key => $value) {
                    if(is_array($value)) {
                        $query->whereIn($key, $value);
                    } else {
                        $query->where($key, $value);
                    }
                }
            }

            return $query->get()->getRowArray();

        } catch(DatabaseException $e) {}

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
    public function updateRecord($id, $data, $where = []) {

        try {

            if(!empty($where)) {
                foreach($where as $key => $value) {
                    $this->where($key, $value);
                }
            }
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