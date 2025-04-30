<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\DbTables;
use CodeIgniter\Database\Exceptions\DatabaseException;

class TicketsModel extends Model {

    protected $table;
    protected $userTable;
    protected $sitesTable;
    protected $feedbackTable;
    protected $allowedFields = [
        'account_id', 'user_id', 'record_type', 'title', 'message', 'status', 'ticket_id', 'department', 
        'site_id', 'created_at', 'closed_at', 'closed_by', 'last_updated'
    ];

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$ticketsTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }
    /**
     * Get comments
     * 
     * @param int $ticketId
     * 
     * @return array
     */
    public function getComments($ticketId) {

        try {

            $query = $this->db->table("{$this->table} a")
                ->select('a.id, a.account_id, a.user_id, a.status, a.title, a.message, a.department, a.created_at, u.admin_access, u.full_name AS created_by_name, a.closed_at')
                ->join("{$this->userTable} u", 'u.id = a.user_id')
                ->where('a.status !=', 'closed')
                ->where('a.ticket_id', $ticketId)
                ->where('a.record_type', 'comment')
                ->orderBy('a.created_at', 'DESC');

            return $query->get()->getResultArray();

        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create a record
     * 
     * @param array $data
     * 
     * @return int
     */
    public function createRecord($data) {
        $this->insert($data);
        return $this->db->insertID();
    }

    /**
     * Create a comment
     * 
     * @param array $data
     * 
     * @return int
     */
    public function createComment($data) {
        $this->insert($data);
        return $this->db->insertID();
    }

    /**
     * Get feedbacks
     * 
     * @return array
     */
    public function getFeedbacks() {
        try {
            $query = $this->db->table("{$this->feedbackTable} f")
            ->select("f.id, f.user_id, f.message, f.created_at, u.full_name")
            ->join("{$this->userTable} u", "u.id = f.user_id")
            ->where("f.status !=", 'deleted')
            ->orderBy('f.created_at', 'DESC');

            return $query->get()->getResultArray();

        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get recent feedback count
     * 
     * @param int $userID
     * 
     * @return array
     */
    public function recentFeedbackCount($userID) {
        try {
            $query = $this->db->table($this->feedbackTable)
            ->select('COUNT(*) AS feedbackCount')
            ->where('user_id', $userID)
            ->where('status !=', 'deleted')
            ->where('DATE(created_at)', date('Y-m-d'));

            return $query->get()->getRowArray();

        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * Create a feedback
     * 
     * @param array $data
     * 
     * @return int
     */
    public function createFeedback($data) {
        $this->db->table($this->feedbackTable)->insert($data);
        return $this->db->insertID();
    }
}