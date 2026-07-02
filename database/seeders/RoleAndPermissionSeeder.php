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

        // Permissions
        $addProduct        = Permission::findOrCreate('add-product');
        $deleteProduct     = Permission::findOrCreate('delete-product');
        $viewInventory     = Permission::findOrCreate('view-inventory');
        $manageAccounts    = Permission::findOrCreate('manage-accounts');
        $viewAllSales      = Permission::findOrCreate('view-all-sales');
        $viewSelfSales     = Permission::findOrCreate('view-Self-Sales');
        $acceptOrder       = Permission::findOrCreate('accept-order');
        $viewSelfInventory = Permission::findOrCreate('view-self-inventory');
        $createOrder       = Permission::findOrCreate('create-order'); 

        // Roles
        $ownerRole    = Role::findOrCreate('owner');
        $employeeRole = Role::findOrCreate('employee');
        $doctorRole   = Role::findOrCreate('doctor');
        $managerRole  = Role::findOrCreate('manager');
        $deliveryRole = Role::findOrCreate('delivery');

        $ownerRole->givePermissionTo([$addProduct, $deleteProduct, $viewInventory, $manageAccounts, $viewAllSales, $acceptOrder]);
        
        $employeeRole->givePermissionTo([$viewSelfSales]);

        $managerRole->givePermissionTo([$viewSelfSales, $viewSelfInventory]);

        $doctorRole->givePermissionTo([$createOrder]);
    }
}