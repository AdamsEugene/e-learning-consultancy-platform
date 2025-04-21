<?php

namespace App\Models;

use App\Models\DbTables;
use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class DiscussionsModel extends Model {

    protected $table;
    protected $primaryKey = 'id';
    protected $coursesTable;
    protected $userTable;
    protected $allowedFields = ['user_id', 'course_id', 'lesson_id', 'votes', 'parent_id', 'content', 'created_at', 'updated_at'];

    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$discussionsTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

}