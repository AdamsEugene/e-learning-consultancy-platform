<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\DbTables;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AccessModel extends Model {
    
    protected $table;
    protected $primaryKey = 'idaccess';
    protected $allowedFields = ['login', 'idsite', 'access', 'account_id'];

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$accessTable;
    }

    /**
     * This method deletes the access permissions for a website
     * 
     * @param int $site
     * @return bool
     */
    public function deleteAccessPermissions($site) {
        return $this->db->table($this->table)
                        ->where('idsite', $site)
                        ->delete();
    }

    /**
     * This method deletes the access permissions for a website
     * 
     * @param int $site
     * @param string $login
     * @return bool
     */
    public function deleteAccessBySiteAndLogin($site, $login) {
        return $this->db->table($this->table)
                        ->where('idsite', $site)
                        ->where('login', $login)
                        ->delete();
    }

    /**
     * This method deletes the access permissions for a website
     * 
     * @param string $login
     * @param int $accountId
     * @return bool
     */
    public function deleteAccessByLoginAndAccountId($login, $accountId) {
        return $this->db->table($this->table)
                        ->where('login', $login)
                        ->where('account_id', $accountId)
                        ->delete();
    }

    /**
     * This method creates a new access record
     * 
     * @param array $data
     * @return bool
     */
    public function createRecord($data) {
        // insert the record
        $this->db->table($this->table)->insert($data);
        
        // return the insert id
        $this->db->insertID();
    }

}