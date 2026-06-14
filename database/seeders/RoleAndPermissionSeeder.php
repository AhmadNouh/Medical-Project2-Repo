<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //Permission
        $addProduct     = Permission::create(['name' => 'add-product']);
        $deleteProduct  = Permission::create(['name' => 'delete-product']);
        $viewInventory  = Permission::create(['name' => 'view-inventory']);
        $manageAccounts = Permission::create(['name' => 'manage-accounts']);
        $viewAllSales = Permission::create(['name' => 'view-all-sales']);
        $viewSelfSales = Permission::create(['name' => 'view-Self-Sales']);
        $acceptOrder = Permission::create(['name' => 'accept-order']);
        $viewSelfInventory = Permission::create(['name' => 'view-self-inventory']);

        // Roles
        $ownerRole    = Role::create(['name' => 'owner']);
        $employeeRole = Role::create(['name' => 'employee']);
        $doctorRole   = Role::create(['name' => 'doctor']);
        $managerRole = Role::create(['name' => 'manager']);
        $deliveryRole = Role::create(['name' => 'delivery']);

        $ownerRole->givePermissionTo([$addProduct, $deleteProduct, $viewInventory, $manageAccounts , $viewAllSales , $acceptOrder]);
        
        $employeeRole->givePermissionTo([$viewSelfSales]);

        $managerRole->givePermissionTo([$viewSelfSales , $viewSelfInventory]);
    }
}