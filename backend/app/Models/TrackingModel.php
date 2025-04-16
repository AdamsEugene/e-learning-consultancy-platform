<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\DbTables;
use CodeIgniter\Database\Exceptions\DatabaseException;

class TrackingModel extends Model
{

    protected $table;
    protected $settingsTable;
    protected $allowedFields = ['idsite', 'website_url', 'data'];

    public function __construct() {
        parent::__construct();
        
        $this->table = DbTables::$websiteCachingTable;
        $this->settingsTable = DbTables::$settingsTable;
    }
    /**
     * Find the record by idsite
     * 
     * @param int $idsite
     * @return array
     */
    public function findBySite($idsite) {
        return $this->where('idsite', $idsite)->get()->getRowArray();
    }

    /**
     * Create a new record
     * 
     * @param array $data
     * @return bool
     */
    public function createRecord($data) {
        return $this->insert($data);
    }

    /**
     * Update a record
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateRecord($id, $data) {
        return $this->update($id, $data);
    }

    /**
     * Create a session tag record
     * 
     * @param string $key
     * @param string $value
     * @param array $data
     * @param string|null $appendQuery
     * @param int|null $variant_id
     * @param array $variant_url2
     * @return bool
     */
    public function createSessionTagRecord($key, $value, $data, $appendQuery = null, $variant_id = null, $variant_url2 = []) {
        // continue
		$value = esc($value);
		$idsite = (int)esc($data['idsite']);
		$idsitehsr = (int)esc($data['idsitehsr']);

		// if no hsr ignore the row
		if(empty($value) || empty($idsitehsr)) {
			return;
		}

		// generate the request key to insert
		$request_key = md5("{$data['idsite']}_{$data['idsitehsr']}:{$key}={$value}:{$variant_id}");

        // insert the record
        return $this->db->query("INSERT INTO session_tags 
            SET idsite = {$idsite}, idsitehsr = {$idsitehsr}, tag = '{$key}', 
            value = '{$value}', request_key = '{$request_key}' {$appendQuery}
            ".(!empty($variant_url2) ? "ON DUPLICATE KEY UPDATE variant_url_v2 = '".json_encode($variant_url2)."'" : null)." 
        ");
    }

    /**
     * Save the settings
     * 
     * @param array $data
     * @return bool
     */
    public function saveSettings($data) {
        return $this->db->table($this->settingsTable)->insert($data);
    }

    /**
     * Delete the settings by options
     * 
     * @param array $options
     * @return bool
     */
    public function deleteSettings($options) {
        return $this->db->table($this->settingsTable)->whereIn('options', $options)->delete();
    }

    /**
     * Get the transaction trace
     * 
     * @param int $idorder
     * @param int $idsite
     * @return array
     */
    public function getTransactionTrace($idorder, $idsite) {
        return $this->db->query("SELECT * FROM worker_log WHERE idorder = '{$idorder}' AND idsite = '{$idsite}'")->getRowArray();
    }

    /**
     * Create a transaction record
     * 
     * @param string $transaction
     * @param string $hash
     * @param string $created_at
     * @param int $idorder
     * @param int $idsite
     * @return bool
     */
    public function createTransactionRecord($transaction, $hash, $created_at, $idorder, $idsite) {
        
        // insert the record
        $this->db->table('worker_log')->insert([
            'request_type' => 'transaction',
            'content' => $transaction,
            'hash' => $hash,
            'created_at' => $created_at,
            'idorder' => $idorder,
            'idsite' => $idsite
        ]);

        // return the insert id
        return $this->db->insertID();
    }

    /**
     * Update a transaction record
     * 
     * @param int $id
     * @param string $transaction
     * @param string $hash
     * @return bool
     */
    public function updateTransactionRecord($id, $transaction, $hash) {
        return $this->db->table('worker_log')->update($id, [
            'content' => $transaction,
            'hash' => $hash
        ]);
    }

}
