<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //CREACIÖN DE PERMISOS

        // Permission::create(['name' => 'ver-surtido']); 
        // Permission::create(['name' => 'ver-mercado']);
        // Permission::create(['name' => 'ver-inventario']);
        // Permission::create(['name' => 'ver-gestor']);

        // Permission::create(['name' => 'ver-publicador']);
        // Permission::create(['name' => 'ver-actualizador']);
        // Permission::create(['name' => 'ver-costos']);
        // Permission::create(['name' => 'ver-talleres']);
        // Permission::create(['name' => 'ver-exhibicion']);
        // Permission::create(['name' => 'ver-inversiones']);
        // Permission::create(['name' => 'ver-cotizador']);
        // Permission::create(['name' => 'ver-ventas']);
        // Permission::create(['name' => 'ver-soporte']);
        // Permission::create(['name' => 'ver-envios']);
        // Permission::create(['name' => 'ver-comparador']);
        // Permission::create(['name' => 'ver-alerta-precios']);

    
        //CREACIÖN DE ROLES

        // //ADMIN ROLE
        // $admin = Role::create(['name' => 'admin']);
        // $admin->givePermissionTo(Permission::all());

        // //GESTOR ROLE
        // $gestor = Role::create(['name' => 'gestor']);
        // $gestor->givePermissionTo('ver-gestor');
        // $gestor->givePermissionTo('ver-inventario');

        // //INVENTARIO
        // $inventario = Role::create(['name' => 'inventario']);
        // $inventario->givePermissionTo('ver-inventario');

        // //SURTIDOR ROLE
        // $surtidor = Role::create(['name' => 'surtidor']);
        // $surtidor->givePermissionTo('ver-surtido');
        // $surtidor->givePermissionTo('ver-mercado');




    }
}
