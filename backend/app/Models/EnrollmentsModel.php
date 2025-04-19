<?php

namespace App\Models;

use App\Models\DbTables;
use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class EnrollmentsModel extends Model {

    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $coursesTable;
    protected $userTable;
    protected $allowedFields = ['user_id', 'course_id', 'status', 'created_at', 'updated_at'];

    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$enrollmentsTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Get all enrollments
     * 
     * @return array
     */
    public function getRecords($data = [], $limit = 24, $offset = 0) {
        try {
            $query = $this->db->table("{$this->table} as e")->select('e.*');

            if(!empty($data)) {
                foreach($data as $key => $value) {
                    if(is_array($value)) {
                        $query->whereIn("e.{$key}", $value);
                    } else {
                        $query->where("e.{$key}", $value);
                    }
                }
            }

            $query->orderBy('e.created_at', 'DESC');

            return $query->get($limit, $offset)->getResultArray();

        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create a record
     * 
     * @param array $data
     * @return int
     */
    public function createRecord($data) {
        try {
            return $this->db->table($this->table)->insert($data);
        } catch(DatabaseException $e) {
            return false;
        }
    }

}
