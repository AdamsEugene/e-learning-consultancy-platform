<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\DbTables;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PaymentsModel extends Model {

    protected $table;
    protected $payment_token;
    protected $allowedFields = ['user_id', 'card_id', 'card_info', 'is_default_card'];

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$paymentsTable;
        $this->payment_token = DbTables::$paymentsTokenTable;
    }

    /**
     * Update the record
     * 
     * @param array $whereClause
     * @param array $data
     * 
     * @return object
     */
    public function updateRecord($whereClause, $data) {
        try {
            return $this->db->table($this->table)->where($whereClause)->update($data);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Fetch a single payment method
     * 
     * @param array $whereClause
     * @param string $appendSelect
     * 
     * @return object
     */
    public function fetchSinglePaymentMethod($select, $whereClause) {
        try {
            return $this->db->table($this->table)
                        ->select($select)
                        ->where($whereClause)
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getRowArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Fetch all payment methods
     * 
     * @param string $select
     * @param array $whereClause
     * 
     * @return object
     */
    public function fetchAllPaymentMethods($select, $whereClause, $limit = 100) {
        try {
            return $this->db->table($this->table)
                        ->select($select)
                        ->where($whereClause)
                        ->orderBy('id', 'DESC')
                        ->limit($limit)
                        ->get()->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Find the token
     * 
     * @param string $token
     * 
     * @return object
     */
    public function findToken($token) {
        try {
            return $this->db->table($this->payment_token)->where('token', $token)->get()->getResult();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Find the token
     * 
     * @param string $token
     * 
     * @return object
     */
    public function findTokenWithUserId($userId) {
        try {
            return $this->db->table($this->payment_token)
                            ->where('user_id', $userId)
                            ->orderBy('id', 'DESC')
                            ->limit(1)
                            ->get()
                            ->getRowArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

     /**
     * Find the token
     * 
     * @param string $token
     * 
     * @return object
     */
    public function deleteToken($token) {
        try {
            return $this->db->table($this->payment_token)->where('token', $token)->delete();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Insert the token
     * 
     * @param array $data
     * 
     * @return object
     */
    public function insertToken($data) {
        try {
            $this->db->table($this->payment_token)->insert($data);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }
}
