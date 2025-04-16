<?php
namespace App\Libraries\Traits;

trait HasPermissions
{
    private ?string $permissionClass = null;
    private ?string $wildcardClass = null;
    private array $wildcardPermissionsIndex = [];

    /**
     * Get the permission class name
     */
    public function getPermissionClass(): string
    {
        if (!$this->permissionClass) {
            $this->permissionClass = config('Permissions')->permissionClass ?? 'App\Models\PermissionsModel';
        }

        return $this->permissionClass;
    }

    /**
     * Initialize the wildcard permissions index
     */
    protected function initializeWildcardPermissions()
    {
        if (!empty($this->wildcardPermissionsIndex)) {
            return;
        }

        $allPermissions = $this->getAllPermissions();

        foreach ($allPermissions as $permission) {
            $name = is_array($permission) ? $permission['name'] : $permission->name;
            $this->addWildcardPermissionToIndex($name);
        }
    }

    /**
     * Add a permission to the wildcard index
     */
    protected function addWildcardPermissionToIndex(string $permission)
    {
        $parts = explode('.', $permission);
        $last = count($parts);
        $current = &$this->wildcardPermissionsIndex;

        foreach ($parts as $i => $part) {
            $isLast = ($i === $last - 1);

            if (!isset($current[$part])) {
                $current[$part] = $isLast ? [] : ['*' => []];
            }

            if (!$isLast) {
                $current = &$current[$part];
            }
        }
    }

    /**
     * Check if a permission matches any wildcard patterns
     */
    protected function wildcardMatch(string $permission, string $pattern): bool
    {
        // Direct match
        if ($permission === $pattern) {
            return true;
        }

        // Split into parts
        $permParts = explode('.', $permission);
        $patternParts = explode('.', $pattern);

        // Pattern can't be longer than the permission
        if (count($patternParts) > count($permParts)) {
            return false;
        }

        $lastIndex = count($patternParts) - 1;

        // Check each part
        foreach ($patternParts as $i => $patternPart) {
            // If this is a wildcard
            if ($patternPart === '*') {
                // If this is the last part of the pattern
                if ($i === $lastIndex) {
                    // Only match remaining parts at this same level
                    return count($permParts) === count($patternParts);
                }
                // If not the last part, continue checking next parts
                continue;
            }

            // Non-wildcard parts must match exactly
            if ($patternPart !== $permParts[$i]) {
                return false;
            }
        }

        // Lengths must match unless pattern ended with wildcard
        return count($patternParts) === count($permParts) ||
            (end($patternParts) === '*' && count($patternParts) === count($permParts));
     }

    /**
     * Check if model has the given permission, including wildcard matches
     */
    public function hasPermission($permission): bool
    {
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        // Initialize wildcard permissions
        $this->initializeWildcardPermissions();

        // Convert permission to string if it's an object/array
        if (is_array($permission)) {
            $permissionName = $permission['name'];
        } elseif (is_object($permission)) {
            $permissionName = $permission->name;
        } else {
            $permissionName = $permission;
        }

        // Check all permissions (direct and from roles) for wildcard matches
        $allPermissions = $this->getAllPermissions();
        foreach ($allPermissions as $userPerm) {
            $userPermName = is_array($userPerm) ? $userPerm['name'] : $userPerm->name;

            if ($this->wildcardMatch($permissionName, $userPermName) ||
                $this->wildcardMatch($userPermName, $permissionName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if model has any of the given permissions
     */
    public function hasAnyPermission(...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if model has all of the given permissions
     */
    public function hasAllPermissions(...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Assign permissions to the model
     */
    public function givePermissionTo($permissions)
    {
        $modelId = $this->getModelId();
        if (!$modelId) {
            throw new \RuntimeException('Model must be saved before assigning permissions.');
        }

        $permissions = $this->collectPermissions($permissions);
        if (empty($permissions)) {
            return $this;
        }

        $permissionClass = $this->getPermissionClass();
        $permissionModel = new $permissionClass();
        $db = \Config\Database::connect();

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = $permissionModel->where('name', $permission)->first();
            }

            if (!$permission) {
                continue;
            }

            $permissionId = is_array($permission) ? $permission['id'] : $permission->id;
            $permissionName = is_array($permission) ? $permission['name'] : $permission->name;

            // Check if permission is already assigned
            if (!$this->hasPermission($permission)) {
                $db->table('model_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'model_id' => $modelId,
                    'model_type' => get_class($this)
                ]);

                // Add to wildcard index
                $this->addWildcardPermissionToIndex($permissionName);
            }
        }

        return $this;
    }

    /**
     * Other methods from before...
     */
    protected function collectPermissions($items)
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

    public function permissions()
    {
        $permissionClass = $this->getPermissionClass();
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        // Get direct permissions
        $directPermissions = $db->table('model_has_permissions')
            ->select('permissions.*')
            ->join('permissions', 'permissions.id = model_has_permissions.permission_id')
            ->where('model_has_permissions.model_id', $modelId)
            ->where('model_has_permissions.model_type', get_class($this))
            ->get()
            ->getResultArray();

        // If this model has roles, get permissions from roles too
        if (method_exists($this, 'roles')) {
            $roles = $this->roles();
            foreach ($roles as $role) {
                $rolePermissions = $db->table('role_has_permissions')
                    ->select('permissions.*')
                    ->join('permissions', 'permissions.id = role_has_permissions.permission_id')
                    ->where('role_has_permissions.role_id', $role['id'])
                    ->get()
                    ->getResultArray();

                $directPermissions = array_merge($directPermissions, $rolePermissions);
            }
        }

        // Remove duplicates
        return array_unique($directPermissions, SORT_REGULAR);
    }

    public function getAllPermissions(): array
    {
        return $this->permissions();
    }

    /**
     * Remove permissions from the model
     */
    public function revokePermissionTo(...$permissions)
    {
        $permissions = $this->collectPermissions($permissions);

        if (empty($permissions)) {
            return $this;
        }

        $permissionClass = $this->getPermissionClass();
        $permissionModel = new $permissionClass();
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = $permissionModel->where('name', $permission)->first();
            }

            if (!$permission) {
                continue;
            }

            $permissionId = is_array($permission) ? $permission['id'] : $permission->id;

            $db->table('model_has_permissions')
                ->where('permission_id', $permissionId)
                ->where('model_id', $modelId)
                ->where('model_type', get_class($this))
                ->delete();
        }

        // Reset wildcard index as permissions have changed
        $this->wildcardPermissionsIndex = [];

        return $this;
    }

    /**
     * Sync permissions
     */
    public function syncPermissions(...$permissions)
    {
        $db = \Config\Database::connect();
        $modelId = $this->getModelId();

        $db->table('model_has_permissions')
            ->where('model_id', $modelId)
            ->where('model_type', get_class($this))
            ->delete();

        // Reset wildcard index
        $this->wildcardPermissionsIndex = [];

        return $this->givePermissionTo(...$permissions);
    }
    /**
     * Give permissions to a model using its ID
     *
     * @param int|string $modelId
     * @param mixed $permissions
     * @return void
     */
    public static function givePermissionById($modelId, ...$permissions)
    {
        $instance = new static();
        $instance->id = $modelId;
        return $instance->givePermissionTo(...$permissions);
    }

    /**
     * Give permissions to multiple models using their IDs
     *
     * @param array $modelIds
     * @param mixed $permissions
     * @return void
     */
    public static function givePermissionToMany(array $modelIds, ...$permissions)
    {
        foreach($modelIds as $modelId) {
            static::givePermissionById($modelId, ...$permissions);
        }
    }

    /**
     * Check if a model has a permission
     * 
     * @param int|string $modelId
     * @param string $type
     * @param mixed $permissions
     * @return bool
     */
    public static function checkPermission($modelId, $type, ...$permissions): bool
    {
        $instance = new static();
        $instance->id = $modelId;
        switch ($type) {
            case 'hasAnyPermission':
                return $instance->hasAnyPermission($permissions);
                break;
            case 'hasAllPermission':
                return $instance->hasAllPermissions($permissions);
                break;
            default:
                foreach ($permissions as $permission) {
                    return $instance->hasPermission($permission);
                }
                break;
        }
        return false;
    }
    
}
