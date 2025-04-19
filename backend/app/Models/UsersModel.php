<?php

namespace App\Models;

use App\Libraries\Traits\HasPermissions;
use App\Libraries\Traits\HasRoles;
use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class UsersModel extends Model {
    
    use HasRoles;
    use HasPermissions;

    public $id;

    public $isAdmin = true;
    public $key = "account";
    protected $primaryKey = "id";
    protected $table ;
    protected $altUserTable;
    protected $accessTable;
    protected $accountTable;
    protected $subscriptionTable;
    protected $teamsTable;
    protected $coursesTable;
    protected $authTokenTable;
    protected $paginateObject;
    protected $allowedFields = [
        "username", "email", "firstname", "lastname", "status", "two_factor_setup", "twofactor_secret", "user_type",
        "admin_access", "date_registered", "nationality", "gender", "date_of_birth", "phone",  "password", "billing_address",
        "timezone", "website", "company", "job_title", "description", "skills", "social_links", "language",
        "rating", "students_count", "coursesCount", "image"
    ];
    
    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$userTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Find users
     * 
     * @param int|null $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function findUsers(?int $limit = null, int $offset = 0, ?string $search = null, ?array $status = ['Active'], ?array $userIds = [], ?array $data = [])
    {
        // get query
        $query = $this->limit($limit, $offset);

        // search
        if(!empty($search)) {
            $query->groupStart();
            $query->like('phone', $search);
            // search in phone, email, username, full_name
            foreach (['email', 'username', 'firstname', 'lastname', 'user_type'] as $where) {
                $query->orLike($where, $search);
            }
            $query->groupEnd();
        }

        if(!empty($status)) {
            $status = !is_array($status) ? [$status] : $status;
            $query->whereIn('status', $status);
        }

        if(!empty($userIds)) {
            $query->whereIn('id', $userIds);
        }

        if(!empty($data)) {
            foreach($data as $key => $value) {
                if(is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }
        }

        // order by idsite DESC
        $result = $query->paginate($limit, 'default', $offset);

        // get the paginate object
        $this->paginateObject = $this->pager;

        return $result;
    }

    /**
     * Global find
     * 
     * @param array $data
     * @return array
     */
    public function globalSearchColumn($column, $data) {
        return $this->db->table($this->table)->select($column)->where($data)->get()->getRowArray();
    }

    /**
     * Search by emails
     * 
     * @param array $emails
     * @return array
     */
    public function getAdminsByEmails() {
        try {
            return $this->db->table($this->table)
                            ->select('firstname, lastname, email, username, password')
                            ->where('admin_access', 1)
                            ->whereIn('status', ['Active'])
                            ->where('permissions LIKE "%write%"')
                            ->get()
                            ->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Global find
     * 
     * @param array $data
     * @return array
     */
    public function globalSearch($data) {
        try {
            return $this->db->table($this->table)->select('*')->where($data)->get()->getRowArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Find user by id
     * 
     * @param int $id
     * @return array|null
     */
    public function findById(int $id, array $status = ['Active'], $data = [])
    {
        try {
            $query = $this->where('id', $id)->whereIn('status', $status);

            if(!empty($data)) {
                foreach($data as $key => $value) {
                    $query->where($key, $value);
                }
            }

            return $query->first();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Find user by email
     * 
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email, array $status = ['Active']) {
        try {
            return $this->where(['email' => $email])
                        ->whereIn('status', $status)
                        ->first();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Quick find by login
     * 
     * @param string $login
     * @param array $status
     * 
     * @return array|null
     */
    public function quickFindByLogin($login, array $status = ['Active']) {
        return $this->where(['login' => $login])
                    ->whereIn('status', $status)
                    ->first();
    }

    /**
     * Find user by login
     * 
     * @param string $login
     * @return array|null
     */
    public function findByLogin($login, array $status = ['Active']) {
        try {
            return $this->select("{$this->table}.*, {$this->table}.id as user_id")
                        ->where(["{$this->table}.username" => $login])
                        ->whereIn("{$this->table}.status", stringToArray($status))
                        ->first();
        } catch(DatabaseException $e) {
            print $e->getMessage();
            return [];
        }
    }

    /**
     * Find user by email or login
     * 
     * @param string $email
     * @param string $login
     * @param array $status
     * 
     * @return array|null
     */
    public function findByEmailOrLogin($email, $login, array $status = ['Active']) {
        try {
            return $this->groupStart()->where(['email' => $email])
                        ->orWhere(['login' => $login])
                        ->groupEnd()
                        ->whereIn('status', $status)
                        ->first();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create a record
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function createRecord(array $data) {
        try {
            $this->db->table($this->table)->insert($data);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update a record
     * 
     * @param int $id
     * @param array $data
     * 
     * @return bool
     */
    public function updateRecord(int $id, array $data) {
        try {
            return $this->db->table($this->table)->where(['id' => $id])->update($data);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Delete alt user
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function deleteAltUser(array $data) {
        try {
            return $this->db->table($this->altUserTable)->where($data)->delete();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Insert alt user
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function insertAltUser(array $data) {
        try {
            return $this->db->table($this->altUserTable)->insert($data);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get alt user
     * 
     * @param array $data
     * 
     * @return array|null
     */
    public function getAltUser(array $data) {
        try {
            return $this->db->table($this->altUserTable)->where($data)->get()->getRowArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Update record by email
     * 
     * @param string $email
     * @param array $data
     * 
     * @return bool
     */
    public function updateRecordByEmail(string $email, array $data) {
       try {
            return $this->db->table($this->table)->where(['email' => $email])->update($data);
       } catch(DatabaseException $e) {
            return false;
       }
    }

    /**
     * Delete a record
     * 
     * @param int $id
     * 
     * @return bool
     */
    public function deleteRecord(array $data) {
        try {
            return $this->db->table($this->table)->where($data)->delete();
        } catch(DatabaseException $e) {
            return false;
        }
    }

}
?>
