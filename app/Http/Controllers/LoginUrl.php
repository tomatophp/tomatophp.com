<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Features\UserImpersonation;

class LoginUrl extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'token' => "required|string",
            'email' => "required|string|email|max:255",
        ]);

        $tenant = \Modules\Core\Models\Tenant::where('email', $request->get('email'))->first();
        if($tenant){
            $user =  \App\Models\User::find(1);
            $user->update([
                'name' => $tenant->name,
                'email' => $tenant->email,
                'packages' => $tenant->packages,
                'password' => $tenant->password,
            ]);

            $permissions = [];
            if(in_array('filament-users', $user->packages)){
                $permissions  = array_merge($permissions, $this->generatePermissions('user'));
            }
            if(in_array('filament-translations', $user->packages)){
                $permissions  = array_merge($permissions, $this->generatePermissions('translation'));
            }
            if(in_array('filament-notes', $user->packages)){
                $permissions  = array_merge($permissions, $this->generatePermissions('note'));
            }

            $role = Role::query()->where('name', 'super_admin')->first();
            if(!$role){
                $role = Role::query()->create([
                    'name' => 'super_admin',
                    'guard_name' => 'web',
                ]);

                $role->permissions()->sync($permissions);
            }

            $user->roles()->sync($role->id);

            if($tenant->name){
                $site = new \TomatoPHP\FilamentSettingsHub\Settings\SitesSettings();
                $site->site_name = $tenant->name;
                $site->save();
            }
        }

        return UserImpersonation::makeResponse($request->get('token'));
    }

    public function generatePermissions(string $table)
    {
        $array = [
            'view_' . $table,
            'view_any_' . $table,
            'create_' . $table,
            'update_' . $table,
            'restore_' . $table,
            'restore_any_' . $table,
            'replicate_' . $table,
            'reorder_' . $table,
            'delete_' . $table,
            'delete_any_' . $table,
            'force_delete_' . $table,
            'force_delete_any_' . $table,
        ];

        $permissionsIds=[];
        foreach ($array as $value) {
           $check = Permission::query()->where('name', $value)->first();
           if(!$check){
                $getId = Permission::query()->create([
                    'name' => $value,
                    'guard_name' => 'web',
                ]);

               $permissionsIds[] = $getId->id;
           }
           else {
               $permissionsIds[] = $check->id;
           }
        }

        return $permissionsIds;
    }
}
