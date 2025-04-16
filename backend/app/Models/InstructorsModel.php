<?php 

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;
class InstructorsModel extends Model {

    protected $table;
    protected $userTable;
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'instructor_id', 'created_at', 'updated_at'];

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$instructorsTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Get records
     * 
     * @param int $limit
     * @param int $offset
     * @param array $data
     * @return array
     */
    public function getRecords($limit = 100, $offset = 0, $data = []) {
        try {
            $query = $this->select("{$this->table}.*, u.firstname, u.lastname, u.email, u.phone, u.image")
                        ->join("{$this->userTable} u", 'u.id = instructors.instructor_id', 'left');

            if(isset($data['course_id'])) {
                $query->where('course_id', $data['course_id']);
            }

            if(isset($data['instructor_id'])) {
                $query->where('instructor_id', $data['instructor_id']);
            }

            $query->limit($limit, $offset);

            return $query->get()->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get record
     * 
     * @param int $id
     * @param array $data
     * @return array
     */
    public function getRecord($id, $data = []) {
        try {
            $query = $this->select("{$this->table}.*, u.firstname, u.lastname, u.email, u.phone, u.image")
                        ->join("{$this->userTable} u", 'u.id = instructors.instructor_id', 'left');

            if(isset($data['course_id'])) {
                $query->where('course_id', $data['course_id']);
            }

            $query->where('id', $id);

            if(isset($data['instructor_id'])) {
                $query->where('instructor_id', $data['instructor_id']);
            }

            return $query->get()->getRowArray();
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
            $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return 0;
        }
    }

    /**
     * Update record
     * 
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateRecord($id, $data) {
        try {
            $this->db->table($this->table)->where('id', $id)->update($data);
            return $this->db->affectedRows();
        } catch(DatabaseException $e) {
            return 0;
        }
    }

    /**
     * Delete record
     * 
     * @param int $id
     * @return int
     */
    public function deleteRecord($id) {
        try {
            $this->db->table($this->table)->where('id', $id)->delete();
            return $this->db->affectedRows();
        } catch(DatabaseException $e) {
            return 0;
        }
    }
}