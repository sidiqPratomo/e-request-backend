<?php

namespace Database\Seeders;

use App\Models\Privileges;
use App\Models\RolePrivileges;
use App\Models\Roles;
use App\Models\RoleUsers;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserManagement extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleexists = Roles::first();
        $privilegeexists = Privileges::first();
        $rolePrivilegeexists = RolePrivileges::first();
        $userexists = User::first();
        $roleuserexists = RoleUsers::first();
        if ($roleexists || $privilegeexists || $rolePrivilegeexists || $userexists || $roleuserexists) {
            return;
        }
        $role = [
            'name' => 'admin',
            'code' => 'administrator',
        ];

        Roles::create($role);

        $privilege = [
            'module' => 'administrator',
            'submodule' => 'administrator',
            'ordering' => '1',
            'action' => 'All access',
            'method' => 'all',
            'uri' => '*',
        ];

        Privileges::create($privilege);

        $rolePrivilege = [
            'role' => '1',
            'action' => 'all',
            'uri' => '*',
            'method' => 'all',
        ];

        RolePrivileges::create($rolePrivilege);

        $users = [
            'photo' => null,
            'first_name' => 'Cute',
            'last_name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@mail.co.id',
            'gender' => 'Male',
            'address' => 'Jakarta',
            'phone_number' => '0000',
            'password' => bcrypt('password'),
        ];

        User::create($users);

        $roleUser = [
            'users_id' => 1,
            'roles_id' => 1,
        ];

        RoleUsers::create($roleUser);
    }
}
