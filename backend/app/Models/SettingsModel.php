<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class SettingsModel extends Model
{
    protected $settingsTable;
    protected $sitesTable;
    protected $table;
    protected $ipBlockingTable;
    protected $primaryKey = 'id';
    protected $allowedFields = ['options', 'settings_values', 'idsite'];

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$settingsTable;
        foreach(DbTables::initTables() as $key) {
            if (property_exists($this, $key)) {
                $this->{$key} = DbTables::${$key};
            }
        }
    }

    /**
     * This method creates a new settings record
     * 
     * @param array $data
     * @return bool
     */
    public function createRecord($data) {
        return $this->db->table($this->settingsTable)
                        ->insert($data);
    }

    /**
     * This method updates a settings record
     *
     * @param array $data
     * @param array|String $whereBind
     * @return bool
     */
    public function updateRecord(array $data, array|String $whereBind)
    {
        return $this->db->table($this->settingsTable)
                        ->where($whereBind)
                        ->update($data);
    }

    /**
     * This method finds a settings record by option
     *
     * @param $options
     * @param null $limit
     * @return array
     */
    public function findByOption($options, $limit = null): array
    {
        $options = !is_array($options) ? [$options] : $options;

        return $this->db->table($this->settingsTable)
                        ->whereIn('options', $options)
                        ->limit($limit)
                        ->get()
                        ->getResultArray();
    }

    /**
     * This method finds a settings record by option
     *
     * @param $options
     * @param int $limit
     * @return array
     */
    public function getSiteSettings($whereBind, int $limit = 1): array
    {
        return $this->db->table($this->settingsTable)
            ->where($whereBind)
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get the heatmaps
     *
     * @param array $bind
     * @param int|null $limit
     * @param int $offset
     *
     * @return array
     */
    public function getCustomResponse(array $bind, $select = '*', int $limit = 1, int $offset = 0): array
    {
        try {
            return $this->db->table($this->settingsTable)
                ->select($select)
                ->where($bind)
                ->limit($limit, $offset)
                ->get()->getResultArray();
        } catch (DatabaseException $e) {
            return [];
        }
    }
    
    /**
     * Update the sites table
     * 
     * @param array $data
     * @param array $whereBind
     * @return array
     */
    public function updateSitesTable ($data, $whereBind) {
        try {
            return $this->db->table($this->sitesTable)
                ->where($whereBind)
                ->update($data);
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * Delete the heatmaps groups
     *
     * @param array $bindWhere
     *
     * @return array
     */
    public function deleteIPBlocking($bindWhere) {
        try {
            return $this->db->table($this->ipBlockingTable)
                ->where($bindWhere)
                ->delete();
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * Update the ip blocking table
     * 
     * @param array $data
     * @return array
     */
    public function updateIpBlocking ($data) {
        try {
            return $this->db->table($this->ipBlockingTable)
                ->update($data);
        } catch (DatabaseException $e) {
            return [];
        }
    }

    /**
     * Get active directories
     * 
     * @param string $dataTableName
     * @param int $limit
     * 
     * @return array
     */
    public function getActiveRecords($dataTableName, $limit) {

        try {
            return $this->db->table($dataTableName)->select('*')
                            ->whereNotIn('data_type', ['log_hsr', 'log_visit'])
                            ->orWhere(['status' => 1, 'data_type' => 'recordings_new'])
                            ->limit($limit)
                            ->get()
                            ->getResultArray();
        } catch (DatabaseException $e) {
            return [];
        }

    }
    
}
