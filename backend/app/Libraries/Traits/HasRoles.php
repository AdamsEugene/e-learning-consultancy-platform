<?php
namespace App\Libraries\Traits;

use CodeIgniter\Database\BaseBuilder;

trait HasRoles
{
    use HasPermissions;
    private ?string $roleClass = null;

    /**
     * Boot the trait
     */
    public static function bootHasRoles()
    {
        // Initialize any needed setup
    }

    /**
     * Get the role class name
     */
    public function getRoleClass(): string
    {
        if (!$this->roleClass) {
            $this->roleClass = config('Roles')->roleClass ?? 'App\Models\RolesModel';
        }

        return $this->roleClass;
    }

    /**
     * Get model ID safely
     */
    protected function getModelId()
    {
        if (is_array($this->attributes)) {
            return $this->attributes['id'] ?? null;
        }
        return $this->id ?? null;
    }

    /**
     * Assign roles to the model
     *
     * @param mixed ...$roles
     * @return $this
     */
    public function assignRole(...$roles)
    {
        $modelId = $this->getModelId();
        if (!$modelId ) {
            throw new \RuntimeException('Model must be saved before assigning roles.');
        }
        
        
        $roles = $this->collectRoles($roles);
        if (empty($roles)) {
            return $this;
        }

        $roleClass = $this->getRoleClass();
        $roleModel = new $roleClass();
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();


        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = $roleModel->where('name', $role)->first();
            }
        
            if (!$role) {
                continue;
            }

            $roleId = is_array($role) ? $role['id'] : $role->id;

            // Check if role is already assigned
            if (!$this->hasRole($role['id'])) {
                
                $db->table('model_has_roles')->insert([
                    'role_id' => $roleId,
                    'model_id' => $modelId,
                    'model_type' => get_class($this)
                ]);
            }
       
        }

        return $this;
    }

    /**
     * Convert role input to array
     *
     * @param mixed $items
     * @return array
     */
    public function collectRoles($items)
    {
        if (empty($items)) {
            return [];
        }

        if (is_array($items)) {
            return $items;
        }

        if (is_string($items)) {
            return explode('|', $items);
        }

        return [$items];
    }

    /**
     * Get all roles assigned to the model
     *
     * @return array
     */
    public function roles()
    {
        $roleClass = $this->getRoleClass();
        $roleModel = new $roleClass();
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        $roles = $db->table('model_has_roles')
            ->select('roles.*')
            ->join('roles', 'roles.id = model_has_roles.role_id')
            ->where('model_has_roles.model_id', $modelId)
            ->where('model_has_roles.model_type', get_class($this))
            ->get()
            ->getResultArray();

        return $roles;
    }

    /**
     * Check if model has the given role
     *
     * @param mixed $role
     * @return bool
     */
    public function hasRole($role): bool
    {   
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        $query = $db->table('model_has_roles')
            ->where('model_id', $modelId)
            ->where('model_type', get_class($this));
        
        if (is_string($role)) {
            $roleClass = $this->getRoleClass();
            $roleModel = new $roleClass();
            $roleObj = $roleModel->where('name', $role)->first();
            if (!$roleObj) {
                return false;
            }
            $roleId = is_array($roleObj) ? $roleObj['id'] : $roleObj->id;
            $query->where('role_id', $roleId);
        } elseif (is_array($role)) {

            foreach ($role as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
            return false;
        } else {
//            $roleId = is_array($role) ? $role['id'] : $role->id;
            $roleId = $role;
            $query->where('role_id', $roleId);
        }
        

        return $query->countAllResults() > 0;
    }

    /**
     * Remove roles from the model
     *
     * @param mixed ...$roles
     * @return $this
     */
    public function revokeRole(...$roles)
    {
        $roles = $this->collectRoles($roles);

        if (empty($roles)) {
            return $this;
        }

        $roleClass = $this->getRoleClass();
        $roleModel = new $roleClass();
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = $roleModel->where('name', $role)->first();
            }

            if (!$role) {
                continue;
            }

            $roleId = is_array($role) ? $role['id'] : $role->id;

            $db->table('model_has_roles')
                ->where('role_id', $roleId)
                ->where('model_id', $modelId)
                ->where('model_type', get_class($this))
                ->delete();
        }

        return $this;
    }

    /**
     * Sync roles to the model
     *
     * @param mixed ...$roles
     * @return $this
     */
    public function syncRoles(...$roles)
    {
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        $db->table('model_has_roles')
            ->where('model_id', $modelId)
            ->where('model_type', get_class($this))
            ->delete();

        return $this->assignRole(...$roles);
    }

    /**
     * Get array of role names
     *
     * @return array
     */
    public function getRoleNames(): array
    {
        $roles = $this->roles();
        return array_map(function($role) {
            return is_array($role) ? $role['name'] : $role->name;
        }, $roles);
    }

    /**
     * Get both roles and permissions for the model
     *
     * @return array
     */
    public function getRolesAndPermissions(): array
    {
        // Get all roles as a simple list
        $rolesList = $this->getRoleNames();

        // Initialize permission arrays
        $scopedPermissions = [];
        $otherPermissions = [];

        // Process both role permissions and direct permissions
        $allPermissions = $this->getAllPermissions();

        foreach ($allPermissions as $permission) {
            $permName = is_array($permission) ? $permission['name'] : $permission->name;

            // Check if permission is scoped (contains |)
            if (strpos($permName, '|') !== false) {
                list($scope, $perm) = explode('|', $permName, 2);
                if (!isset($scopedPermissions[$scope])) {
                    $scopedPermissions[$scope] = [];
                }
                $scopedPermissions[$scope][] = $perm;
            } else {
                $otherPermissions[] = $permName;
            }
        }
        $scopedPermissions['other'] = $otherPermissions;
        return [
            'roles' => $rolesList,
            'permissions' => $scopedPermissions
        ];
    }

    /**
     * Get permissions for a specific role
     */
    protected function getRolePermissions($role): array
    {
        $db = \Config\Database::connect();
        $roleId = is_array($role) ? $role['id'] : $role->id;

        return $db->table('role_has_permissions')
            ->select('permissions.*')
            ->join('permissions', 'permissions.id = role_has_permissions.permission_id')
            ->where('role_has_permissions.role_id', $roleId)
            ->get()
            ->getResultArray();
    }

    /**
     * Get only direct permissions (not inherited through roles)
     */
    protected function getDirectPermissions(): array
    {
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        return $db->table('model_has_permissions')
            ->select('permissions.*')
            ->join('permissions', 'permissions.id = model_has_permissions.permission_id')
            ->where('model_has_permissions.model_id', $modelId)
            ->where('model_has_permissions.model_type', get_class($this))
            ->get()
            ->getResultArray();
    }

    /**
     * Assign role to a model using its ID
     *
     * @param int|string $modelId
     * @param mixed $roles
     * @return void
     */
    public static function assignRoleById($modelId, ...$roles)
    {
        $instance = new static();
        $instance->id = $modelId;
        return $instance->assignRole(...$roles);
    }

}
