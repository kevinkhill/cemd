<?php

class CemdController extends Controller {

    /*************************************************************************
     * Dashboard Methods
     *************************************************************************/
    public function getIndex()
    {
        return View::make('cemd.dashboard')
            ->with('rJSON', Role::with('perms')->get())
            ->with('pJSON', Permission::all()->toJSON())
            ->with('uJSON', User::with('roles')->get());
    }

    /*************************************************************************
     * Role Methods
     *************************************************************************/
    public function postRoleCreate()
    {
        $role = new Role;
        $role->name = Input::get('name');
        $role->save();

        if ($role->id) {
            return $this->jsonSuccess(
                'The role of "' + $role->name + '" has been added.',
                $role->toJSON()
            );
        } else {
            return $this->jsonError(
                $role->validationErrors->get('name')[0]
            );
        }
    }

    public function postRoleDelete()
    {
        $roleId = Input::get('roleId');
        $role = Role::find($roleId);

        if ( ! is_null($role)) {
            $role->delete();

            return $this->jsonSuccess(
                'The "' + $role->name + '" role has been deleted.'
            );
        } else {
            return $this->jsonError(
                'Role ID#'.$roldId.' not found.'
            );
        }
    }

    public function postRoleUpdatePermissions()
    {
        $roleId = Input::get('roleId');
        $permissions = Input::get('permissions');

        $role = Role::find($roleId);

        if (!is_null($role)) {
            if ($permissions) {
                $role->perms()->sync($permissions);
            } else {
                $role->perms()->sync(array());
            }

            return $this->jsonSuccess(
                'Permissions have been updated.'
            );
        } else {
            return $this->jsonError(
                "Role ID#{$roleId} not found."
            );
        }
    }

    /*************************************************************************
     * Permission Methods
     *************************************************************************/
    public function postPermissionCreate()
    {
        $permission = new Permission;
        $permission->name = Input::get('name');
        $permission->display_name = Input::get('display_name');
        $permission->save();

        if ($permission->id) {
            return $this->jsonSuccess(
                "The <em>{$permission->display_name}</em> permission has been created.",
                $permission->toJSON()
            );
        } else {
            return $this->jsonError(
                $permission->validationErrors->get('name')
            );
        }
    }

    public function postPermissionDelete()
    {
        $permissionId = Input::get('permissionId');

        $permission = Permission::find($permissionId);

        if (!is_null($permission)) {
            $permission->delete();

            return $this->jsonSuccess(
                "The <em>{$permission->display_name}</em> permission has been deleted."
            );
        } else {
            return $this->jsonError(
                "Permission ID#{$permissionId} not found."
            );
        }
    }

    /*************************************************************************
     * User Methods
     *************************************************************************/
    public function postUserUpdateRoles()
    {
        $userId = Input::get('userId');
        $roles = Input::get('roles');

        $user = User::find($userId);

        if (!is_null($user)) {
            if ($roles) {
                $user->roles()->sync($roles);
            } else {
                $user->roles()->sync(array());
            }

            return $this->jsonSuccess(
                'Roles have been updated.'
            );
        } else {
            return $this->jsonError(
                "User ID#{$userId} not found."
            );
        }
    }

    /*************************************************************************
     * CEMD Methods
     *************************************************************************/
    protected function jsonSuccess($message=null, $data=null)
    {
        return Response::json(array(
            'status'  => 'success',
            'data'    => $data,
            'message' => $message
        ));
    }

    protected function jsonError($message=null, $data=null)
    {
        return Response::json(array(
            'status'  => 'error',
            'data'    => $data,
            'message' => $message
        ));
    }
}
