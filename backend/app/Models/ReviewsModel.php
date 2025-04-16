<?php 
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ReviewsModel extends Model {
    
    protected $table = 'courses_reviews';
    protected $allowedFields = ['course_id', 'user_id', 'rating', 'review', 'likes_count', 'dislikes_count'];

    /**
     * Get all records
     * @param int $courseId
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function getRecords($limit = 10, $offset = 0, $data = []) {
        try {
            $query = $this->orderBy('created_at', 'DESC');

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
     * Get all records
     * @param int $courseId
     * @param int $limit
     * @param int $offset
     * @return array|false
     */
    public function getRecordByCourseId($courseId, $limit = 10, $offset = 0, $data = []) {
        try {
            $query = $this->where('course_id', $courseId);

            if(!empty($data)) {
                foreach($data as $key => $value) {
                    $query->where($key, $value);
                }
            }
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