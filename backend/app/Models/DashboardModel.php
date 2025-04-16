<?php 
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Models\DbTables;

class DashboardModel extends Model {

    protected $statusTable;
    protected $sitesTable;
    protected $dashboardTable;
    protected $userDashboardTable;
    protected $subscriptionTable;
    protected $subscriptionVerifyTable;

    public function __construct() {
        parent::__construct();
        
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Get dashboard data
     * 
     * @param array $payload
     * @return array
     */
    public function getDashboardData($payload = []) {
        try {
            return $this->db->table($this->dashboardTable)
                    ->select('content')
                    ->where($payload)
                    ->limit(1)
                    ->get()->getRowArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get statuses
     * 
     * @return array
     */
    public function getStatuses() {
        try {
            return $this->db->table($this->statusTable)
                        ->select('site_statuses, MAX(created_at) AS created_at')
                        ->where('DATE(created_at) >=', date('Y-m-d', strtotime('-30 days')))
                        ->groupBy('DATE(created_at)')
                        ->orderBy('created_at', 'DESC')
                        ->get()->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get subscription caps
     * 
     * @param int $idSite
     * @return array
     */
    public function getSubscriptionCaps($idSite) {
        try {
            return $this->db->table($this->subscriptionVerifyTable)
                        ->select('subscription_caps, overrun_dates')
                        ->where('idsite', $idSite)
                        ->orderBy('id', 'DESC')
                        ->limit(1)
                        ->get()->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get overrun dates
     * 
     * @param int $idSite
     * @return array
     */
    public function getOverrunDates($idSite) {
        try {
            return $this->db->table($this->sitesTable)
                        ->select('last_overrun_date, overrun_status')
                        ->where('idsite', $idSite)
                        ->get()->getResultArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get user dashboard data
     * 
     * @param int $idUser
     * @param int $idSite
     * @return array
     */
    public function getUserDashboardData($idUser, $idSite) {
        try {
            return $this->db->table($this->userDashboardTable)
                    ->where('user_id', $idUser)
                    ->where('idsite', $idSite)
                    ->get()->getRowArray();
        } catch(DatabaseException $e) {
            return [];
        }
    }

    /**
     * Insert user dashboard data
     * 
     * @param int $idUser
     * @param int $idSite
     * @param array $data
     * @return void
     */
    public function insertUserDashboardData($idUser, $idSite, $data) {
        try {
            $this->db->table($this->userDashboardTable)
                ->insert([
                    'user_id' => $idUser,
                    'idsite' => $idSite,
                    'data' => json_encode($data),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
            // return the insert id
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update user dashboard data
     * 
     * @param int $idUser
     * @param int $idSite
     * @param array $data
     * @return void
     */
    public function updateUserDashboardData($idUser, $idSite, $data) {
        try {
            return $this->db->table($this->userDashboardTable)
                    ->where('user_id', $idUser)
                    ->where('idsite', $idSite)
                    ->update(['data' => json_encode($data), 'updated_at' => date('Y-m-d H:i:s')]);
        } catch(DatabaseException $e) {
            return false;
        }
    }

}
?>