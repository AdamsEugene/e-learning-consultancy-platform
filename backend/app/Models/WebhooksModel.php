<?php 

namespace App\Models;

use CodeIgniter\Model;
use App\Models\DbTables;
use CodeIgniter\Database\Exceptions\DatabaseException;

class WebhooksModel extends Model {

    protected $table;
    protected $sitesTable;
    protected $couponTable;
    protected $webhookTable;
    protected $invoiceTable;
    protected $paymentsTable;
    protected $userTable;
    protected $subscriptionTable;

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$webhookTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * Create the invoice record
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function createInvoice($data) {
        try {
            $this->db->table($this->invoiceTable)->insert($data);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get the invoice record
     * 
     * @param array $bind
     * 
     * @return array
     */
    public function getInvoice($bind) {
        try {
            return $this->db->table($this->invoiceTable)
                            ->where($bind)
                            ->get()
                            ->getRowArray();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update the coupon record
     * 
     * @param array $bind
     * @param array $where
     * 
     * @return bool
     */
    public function updateCoupon($bind, $where) {
        try {
            return $this->db->table($this->couponTable)->update($bind, $where);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Create a record
     * 
     * @param array $bind
     * 
     * @return int
     */
    public function createRecord($bind) {
        try {
            $this->db->table($this->webhookTable)->insert($bind);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Create the coupon record
     * 
     * @param array $bind
     * 
     * @return int
     */
    public function createCoupon($bind) {
        try {
            $this->db->table($this->couponTable)->insert($bind);
            return $this->db->insertID();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get the user record
     * 
     * @param array $bind
     * 
     * @return array
     */
    public function getUser($bind) {
        try {
            return $this->db->table($this->userTable)
                            ->select("id, full_name, email, klaviyo_id, phone, nationality, billing_address")
                            ->where($bind)
                            ->get()
                            ->getRowArray();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update the site record
     * 
     * @param array $bind
     * @param array $where
     * 
     * @return bool
     */
    public function updateSiteRecord($bind, $where) {
        try {
            return $this->db->table($this->sitesTable)->update($bind, $where);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get the site record
     * 
     * @param array $bind
     * 
     * @return array
     */
    public function getSiteRecord($bind) {
        try {
            return $this->db->table($this->sitesTable)
                            ->where($bind)
                            ->get()
                            ->getRowArray();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Update the subscription record
     * 
     * @param array $bind
     * @param array $where
     * 
     * @return bool
     */
    public function updateSubscription($bind, $where) {
        try {
            return $this->db->table($this->subscriptionTable)->update($bind, $where);
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get the subscription record
     * 
     * @param array $bind
     * 
     * @return array
     */
    public function getSubscription($bind) {
        try {
            return $this->db->table("{$this->subscriptionTable} a")
                            ->select("a.*, b.name AS website_name, b.main_url")
                            ->join("{$this->sitesTable} b", 'b.subscription_id = a.id', 'LEFT')
                            ->where($bind)
                            ->get()
                            ->getRowArray();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get the coupon record
     * 
     * @param array $bind
     * 
     * @return array
     */
    public function getCoupon($bind) {
        try {
            return $this->db->table($this->couponTable)
                            ->where($bind)
                            ->get()
                            ->getRowArray();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Delete the payment method record
     * 
     * @param array $bind
     * 
     * @return bool
     */
    public function deletePaymentMethod($bind) {
        try {
            return $this->db->table($this->paymentsTable)->where($bind)->delete();
        } catch(DatabaseException $e) {
            return false;
        }
    }

    /**
     * Get the payment method record
     * 
     * @param array $bind
     * 
     * @return array
     */
    public function getPaymentMethod($bind) {
        try {
            return $this->db->table($this->paymentsTable)
                            ->where($bind)
                            ->get()
                            ->getRowArray();
        } catch(DatabaseException $e) {
            return false;
        }
    }
}

?>