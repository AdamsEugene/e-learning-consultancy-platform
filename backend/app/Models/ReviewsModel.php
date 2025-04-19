<?php 
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ReviewsModel extends Model {
    
    protected $table;
    protected $primaryKey = 'id';
    protected $coursesTable;
    protected $userTable;
    protected $allowedFields = ['record_id', 'user_id', 'rating', 'content', 'helpfulCount', 'dislikesCount', 'entityType'];

    public function __construct() {
        parent::__construct();

        $this->table = DbTables::$reviewsTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Get all records
     * @param int $courseId
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function getRecords($limit = 10, $offset = 0, $data = [], $courseData = false) {
        try {

            if(!empty($courseData)) {
                $query = $this->select("{$this->table}.*, 
                    CASE
                        WHEN {$this->table}.entityType = 'Course' THEN JSON_OBJECT(
                            'title', c.title,
                            'slug', c.title_slug,
                            'image', c.image,
                            'description', c.description,
                            'created_at', c.created_at,
                            'reviewCount', c.reviewCount,
                            'rating', c.rating,
                            'price', c.price,
                            'originalPrice', c.originalPrice
                        )
                        WHEN {$this->table}.entityType = 'Instructor' THEN JSON_OBJECT(
                            'id', u.id, 'firstname', u.firstname, 
                            'lastname', u.lastname, 
                            'email', u.email,
                            'reviewCount', u.reviewCount,
                            'userType', u.user_type,
                            'rating', u.rating
                        )
                    END AS entity,
                 (SELECT JSON_OBJECT(
                    'id', u.id, 'firstname', u.firstname, 'lastname', u.lastname, 'email', u.email
                ) FROM {$this->userTable} u WHERE u.id = {$this->table}.user_id LIMIT 1) as user")
                 ->join('courses c', "c.id = {$this->table}.record_id AND {$this->table}.entityType = 'Course'", 'left')
                 ->join('users u', "u.id = {$this->table}.record_id AND {$this->table}.entityType = 'Instructor'", 'left');
            } else {
                $query = $this->orderBy('created_at', 'DESC');
            }

            if(!empty($data)) {
                foreach($data as $key => $value) {
                    $query->where("{$this->table}.{$key}", $value);
                }
            }

            $result = $query->findAll($limit, $offset);
            return $result;
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get all records
     * @param int $courseId
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function getRecordByCourseId($limit = 10, $offset = 0, $data = []) {
        try {
            $query = $this->where('record_id', $data['record_id']);

            if(!empty($data)) {
                foreach($data as $key => $value) {
                    $query->where($key, $value);
                }
            }
            return $query->findAll($limit, $offset);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get a record
     * @param int $id
     * @return array|false
     */
    public function getRecord($id) {
        try {
            return $this->find($id);
        } catch(DatabaseException $e) {
            return false;
        }
    }
    
    /**
     * Create a record
     * @param array $data
     * @return int|false
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
     * Update a record
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateRecord($id, $data) {
        try {
            $this->update($id, $data);
            return true;
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Delete a record
     * @param int $id
     * @return bool
     */
    public function deleteRecord($id) {
        try {
            $this->delete($id);
            return true;
        } catch(DatabaseException $e) {
            return false;
        }
    }
}