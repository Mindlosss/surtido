<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Mostrar la lista de usuarios
     */
    public function index()
    {
        $users = User::with(['roles', 'permissions'])->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Formulario para crear un nuevo usuario
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.create', compact('roles', 'permissions'));
    }

    /**
     * Guardar el nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4',
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Asignar rol
        $user->assignRole($request->role);

        // Asignar permisos directos
        if ($request->permissions) {
            $user->givePermissionTo($request->permissions);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /**
     * Formulario de edición de usuario
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Actualizar usuario en la base de datos
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:4',
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Actualizar datos básicos
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password
        ]);

        // Sincronizar rol
        $user->syncRoles([$request->role]);

        // Sincronizar permisos directos
        $permissions = $request->permissions ?? [];
        $user->syncPermissions($permissions);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // Evitar que un admin se elimine a sí mismo
        if (auth()->id() === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // Eliminar permisos y roles antes de eliminar el usuario
        $user->syncRoles([]);
        $user->syncPermissions([]);
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}