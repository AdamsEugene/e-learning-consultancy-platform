<?php

namespace App\Models;

use App\Models\DbTables;
use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class NotesModel extends Model {

    protected $table;
    protected $primaryKey = 'id';
    protected $coursesTable;
    protected $userTable;
    protected $allowedFields = ['user_id', 'course_id', 'lesson_id', 'content', 'created_at', 'updated_at'];

    public function __construct() {
        parent::__construct();
        $this->table = DbTables::$notesTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

}