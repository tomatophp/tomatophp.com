<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $tenant = tenancy()->tenant;
        $user =  \App\Models\User::query()->where('email', $tenant->email)->first();
        if($user){
            $user->update([
                'name' => $tenant->name,
                'email' => $tenant->email,
                'packages' => $tenant->packages,
                'password' => $tenant->password,
            ]);
        }
        else {
            $user = new User();
            $user->name = $tenant->name;
            $user->email = $tenant->email;
            $user->password = $tenant->password;
            $user->packages = $tenant->packages;
            $user->email_verified_at = Carbon::now();
            $user->save();
        }


        $getUserPackages = json_decode($user->packages);
        if(is_array($getUserPackages)){
            $permissions = [];
            $packages = config('app.packages');
            foreach ($packages as $key=>$package){
                if(in_array($key, $getUserPackages)){
                    foreach ($package['permissions'] as $permission){
                        $permissions  = array_merge($permissions, $this->generatePermissions($permission));
                    }
                }
            }


            $role = Role::query()->where('name', 'super_admin')->first();
            if(!$role){
                $role = Role::query()->create([
                    'name' => 'super_admin',
                    'guard_name' => 'web',
                ]);
            }

            $role->syncPermissions($permissions);
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
        if(str($table)->contains('page')){
            $array = [
                $table
            ];
        }
        else {
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

        }

        $permissionsIds=[];
        foreach ($array as $value) {
            $check = Permission::query()->where('name', $value)->where('guard_name', 'web')->first();
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
