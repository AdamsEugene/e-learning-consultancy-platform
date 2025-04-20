<?php 

namespace App\Models;

use CodeIgniter\Model;
use App\Models\DbTables;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AnalyticsModel extends Model {

    protected $table = 'analytics';
    protected $primaryKey = "id";
    protected $allowedFields = ["recordType", "totalCount", "recordContent", "created_at", "updated_at"];

    /**
     * Get a record by record type
     * 
     * @param string $record
     * @return array|false
     */
    public function getByRecordType($record) {
        try {
            return $this->where('recordType', $record)->first();
        } catch(DatabaseException $e) {
            return false;
        }
    } 

    /**
     * Get all records
     * 
     * @return array|false
     */
    public function getRecords() {
        try {
            return $this->findAll();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Create a record
     * 
     * @param array $record
     * @return int
     */
    public function createRecord($record) {
        try {
            $this->insert($record);
            return $this->getInsertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update a record
     * 
     * @param int $id
     * @param array $record
     * @return bool
     */
    public function updateRecord($id, $record) {
        try {
            return $this->update($id, $record);
        } catch(DatabaseException $e) {
            return false;
        }
    }
}