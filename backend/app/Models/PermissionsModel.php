<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Models\DbTables;
use CodeIgniter\Database\Exceptions\DatabaseException;


class PermissionsModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Create a new permission
     * @param array $data
     * @return bool
     */
    public function createPermission($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all permissions
     * @return array
     */
    public function getPermissions()
    {
        return $this->findAll();
    }

    /**
     * Get a permission
     * @param int $id
     * @return array
     */
    public function getPermission($id)
    {
        return $this->find($id);
    }

    /**
     * Update a permission
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updatePermission($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Delete a permission
     * @param int $id
     * @return bool
     */
    public function deletePermission($id)
    {
        return $this->delete($id);
    }
}
